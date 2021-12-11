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
        case 'sign_out':
            $_SESSION['login'] = false;
            $result['error'] = false;
            break;
        case 'login_system':
            $login_name = isset($_POST['login_name'])?$_POST['login_name']:'';
            $login_password = isset($_POST['login_password'])?$_POST['login_password']:'';
            if($login_name=='admin' && $login_password=='123456'){
                $_SESSION['login'] = true;
                $result['error'] = false;
            }else{
                $result['msg'] = '帳號或密碼錯誤';
            }
            break;
        case 'save_text':
            $uuid = isset($_POST['uuid'])?$_POST['uuid']:'';
            $text = isset($_POST['text'])?$_POST['text']:'';
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
                    'uuid'        => '123456',
                    'user_id'     => $user_data['id'],
                    'create_time' => date('Y-m-d H:i:s')
                );
                $name_arr[] = $name_str;
                $db->insertData('sheet_notify_user_sheet_names',$data);

            }

            $result['error'] = false;
            $result['data'] = implode('.',$name_arr);
            break;

        default:
            # code...
            break;
    }


    end:
        echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);