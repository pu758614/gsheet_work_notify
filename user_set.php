<?php

    $user_list = $db->getUserList();
    $day_conf = array(
        "0" => "週日",
        "1" => "週一",
        "2" => "週二",
        "3" => "週三",
        "4" => "週四",
        "5" => "週五",
        "6" => "週六",
    );

    foreach ($user_list as $key => $user_data) {
        $no              = $key+1;
        $sheet_name_list = $db->getArrayById('sheet_notify_user_sheet_names',"user_id",$user_data['id']);
        $sheet_name_arr  = array();
        foreach ($sheet_name_list as $sheet_name) {
            $sheet_name_arr[] = $sheet_name['name'];
        }
        $notify_day     = isset($day_conf[$user_data['notify_day']])?$day_conf[$user_data['notify_day']]:'';
        $status         = ($user_data['enable_notify']==1)?"啟動":"關閉";
        $sheet_name_str = implode('.',$sheet_name_arr);
        $tpl->newBlock('user_list');
        $tpl->assignArray(array(
            'no'          => $no,
            'name'        => htmlspecialchars($user_data['real_name']),
            'line_name'   => htmlspecialchars($user_data['line_name']),
            'uuid'        => htmlspecialchars($user_data['line_user_uuid']),
            'sheet_names' => htmlspecialchars($sheet_name_str),
            "notify_day"  => htmlspecialchars($notify_day),
            "status"      => htmlspecialchars($status),
            "notify_day_".$user_data['notify_day']       => "selected=''",
            "enable_notify_".$user_data['enable_notify'] => "checked"
        ));
    }