<?php

    $conf_list = array(
        "password",
        "sheet_code",
    );

    $spreadsheet_id   = $db->getConfigValue('sheet_code');
    $user_list_result = $db->getAllSheetUser('U7024af33ac34455f97b39b7bee8b8436');
    $user_list        = isset($user_list_result['data'])?$user_list_result['data']:array();
    $sheet_list  = $db->getGoogleSheet($spreadsheet_id);
    $work_field_conf = array(
        "1"  => "會前禱告",
        "2"  => "司會",
        "3"  => "敬拜主領",
        "4"  => "配唱",
        "5"  => "配唱",
        "6"  => "司琴",
        "7"  => "司鼓",
        "8"  => "視聽",
        "9"  => "視聽",
        "14" => "破冰",
        "15" => "詩歌",
    );

    $work_list = array();

    foreach ($sheet_list as $key => $sheet_data) {
        if($key<=3){

            continue;
        }
        $date = $sheet_data[0];
       // if(strtotime($sheet_data[0])>= strtotime(date('m/d'))){
            foreach ($sheet_data as $sheet_key => $sheet_val) {
                if(in_array($sheet_val,$user_list)){
                    $work_name = isset($work_field_conf[$sheet_key])?$work_field_conf[$sheet_key]:'';
                    $work_list[$date][] = $work_name;
                }
            }
       // }
    }

    $msg = '';
    foreach ($work_list as $work_date => $work_val) {
        $msg .= $work_date."-". implode(',',$work_val)."\n";
    }
    echo '<pre>';
    print_r($msg);
    echo '</pre>';



    $assign_list = array();
    foreach ($conf_list as  $conf_key) {
        $data = $db->getSingleById('sheet_notify_config',"item",$conf_key);
        if(empty($data)){
            continue;
        }
        $assign_list[$conf_key] = $data['value'];
    }

    $tpl->gotoBlock('_ROOT');
    $tpl->assignArray($assign_list);
