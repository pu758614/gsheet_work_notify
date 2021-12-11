<?php

    $user_list = $db->getUserList();
    echo '<pre>';
    print_r($user_list);
    echo '</pre>';

    foreach ($user_list as $key => $user_data) {
        $no = $key+1;
        $sheet_name_list = $db->getArrayById('sheet_notify_user_sheet_names',"user_id",$user_data['id']);
        $sheet_name_arr = array();
        foreach ($sheet_name_list as $sheet_name) {
            $sheet_name_arr[] = $sheet_name['name'];
        }
        $sheet_name_str = implode('.',$sheet_name_arr);
        $tpl->newBlock('user_list');
        $tpl->assignArray(array(
            'no'          => $no,
            'name'        => htmlspecialchars($user_data['real_name']),
            'line_name'   => htmlspecialchars($user_data['line_name']),
            'uuid'        => htmlspecialchars($user_data['line_user_uuid']),
            'sheet_names' => htmlspecialchars($sheet_name_str),
        ));
    }