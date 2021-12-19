<?php

    $conf_list = array(
        "password",
        "sheet_code",
    );
    $c = $db->getConfigValue('sheet_code');

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