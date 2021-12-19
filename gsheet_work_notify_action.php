<?php
    require 'vendor/autoload.php';
    include_once("lib/lib.php");
    include ('lib/common.php');
    $action = isset($_GET['action'])?$_GET['action']:'';
    $result = array(
        "error" => true,
        "msg"   => '',
        "data"  => ''
    );
    $db = new db_lib;
    session_start();
    switch ($action) {
        case 'test_sheet':
            $sheet_code = isset($_POST['sheet_code'])?$_POST['sheet_code']:'';
            $list_data      = $db->getGoogleSheet($sheet_code);
            if(empty($list_data)){
                $result['msg'] = '無效的google試算表代號';
                goto end;
            }
            $result['error'] = false;
            break;
        case 'config_setting':
            $password   = isset($_POST['password'])?$_POST['password']:'';
            $sheet_code = isset($_POST['sheet_code'])?$_POST['sheet_code']:'';
            $list_data      = $db->getGoogleSheet($sheet_code);
            if(empty($list_data)){
                $result['msg'] = '無效的google試算表代號';
                goto end;
            }
            $conf_list = array(
                "pass_word",
                "sheet_code",
            );
            $password_data = $db->getSingleById('sheet_notify_config','item','password');
            if(empty($password_data)){
                $re = $db->insertData('sheet_notify_config',array(
                    'item'        => 'password',
                    'value'       => $password,
                    'create_time' => date('Y-m-d H:i:s'),
                    'modify_time' => date('Y-m-d H:i:s'),
                ));
            }else{
                $re = $db->updateData('sheet_notify_config',array(
                    'value'       => $password,
                    'modify_time' => date('Y-m-d H:i:s'),
                ),array('id'=>$password_data['id']));
            }
            if(!$re){
                $result['msg'] = '更新密碼失敗';
                goto end;
            }
            $sheet_code_data = $db->getSingleById('sheet_notify_config','item','sheet_code');
            if(empty($sheet_code_data)){
                $re = $db->insertData('sheet_notify_config',array(
                    'item'        => 'sheet_code',
                    'value'       => $sheet_code,
                    'create_time' => date('Y-m-d H:i:s'),
                    'modify_time' => date('Y-m-d H:i:s'),
                ));
            }else{
                $re = $db->updateData('sheet_notify_config',array(
                    'value'       => $sheet_code,
                    'modify_time' => date('Y-m-d H:i:s'),
                ),array('id'=>$sheet_code_data['id']));
            }
            if(!$re){
                $result['msg'] = '更新google試算表代號失敗';
                goto end;
            }
            $result['error'] = false;
            break;
        case 'sign_out':
            $_SESSION['login'] = false;
            $result['error'] = false;
            break;
        case 'login_system':
            $login_name     = isset($_POST['login_name'])?$_POST['login_name']:'';
            $login_password = isset($_POST['login_password'])?$_POST['login_password']:'';
            if($login_name=='admin' && $login_password=='123456'){
                $_SESSION['login'] = true;
                $result['error'] = false;
            }else{
                $result['msg'] = '帳號或密碼錯誤';
            }
            break;
        case 'save_text':
            $uuid       = isset($_POST['uuid'])?$_POST['uuid']:'';
            $text       = isset($_POST['text'])?$_POST['text']:'';
            $notify_day = isset($_POST['notify_day'])?$_POST['notify_day']:0;
            $status     = isset($_POST['status'])?$_POST['status']:0;
            $text = convertStrType($text);
            $text = trim($text);
            $text = str_replace(array('.',','),',',$text);
            $name_list = explode(',',$text);
            $user_data = $db->getSingleById('sheet_notify_user','line_user_uuid',$uuid);
            if(empty($user_data)){
                $result['msg'] = '錯誤的uuid';
                goto end;
            }
            $name_save_list = array();
            foreach ($name_list as  $name) {
                $name = trim($name);
                if($name==''){
                    continue;
                }
                if($db->checkNameExist($user_data['id'],$name)){
                    $result['msg'] = $name.'已經重複了';
                    goto end;
                }
                $name_save_list[] = $name;
            }

            $db->deleteData('sheet_notify_user_sheet_names',array("user_id"=>$user_data['id']));
            $name_arr = array();
            foreach ($name_save_list as $name_str) {
                $data = array(
                    'name'        => $name_str,
                    'user_id'     => $user_data['id'],
                    'create_time' => date('Y-m-d H:i:s')
                );
                $name_arr[] = $name_str;
                $db->insertData('sheet_notify_user_sheet_names',$data);
            }
            $db->updateData('sheet_notify_user',array(
                'notify_day'    => $notify_day ,
                'enable_notify' => $status,
                'modify_time'   => date('Y-m-d H:i:s')
            ),array('line_user_uuid'=>$uuid));
            $result['error'] = false;
            $result['data'] = implode('.',$name_arr);
            break;

    }


    end:
        echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);