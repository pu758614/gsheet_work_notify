<?php
    require 'vendor/autoload.php';
    include_once("lib/lib.php");
    include_once("lib/LINEBotTiny.php");
    if (file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
    $db = new db_lib();
    //$spreadsheet_id       = isset($_ENV['SPREADSHEET_ID'])?$_ENV['SPREADSHEET_ID']:'';
    $now_day              = isset($_ENV['NOW_DAY'])?$_ENV['NOW_DAY']:'';
    $channel_access_token = isset($_ENV['CHANNEL_ACCESS_TOKEN'])?$_ENV['CHANNEL_ACCESS_TOKEN']:'';
    $channel_secret       = isset($_ENV['CHANNEL_SECRET'])?$_ENV['CHANNEL_SECRET']:'';
    $spreadsheet_id       = $db->getConfigValue('sheet_code');
    $client = new LINEBotTiny($channel_access_token, $channel_secret);

    $list_data      = $db->getGoogleSheet($spreadsheet_id);

    if(date("l")!='Saturday'){
        $next_day = date("n/j",strtotime("next Saturday"));
    }else{
        $next_day = date("n/j");
    }
    $nex_data = array();
    foreach ($list_data as $key => $data) {
        if($key==0 && $key==1){
            continue;
        }
        if($data[0]==$next_day){
            $nex_data = $data;
        }
    }
    $day_conf = array(
        '0' => "Sunday",
        '1' => "Monday",
        '2' => "Tuesday",
        '3' => "Wednesday",
        '4' => "Thursday",
        '5' => "Friday",
        '6' => "Saturday",
    );
    $day_cn_conf = array(
        "Sunday"    => '0',
        "Monday"    => '1',
        "Tuesday"   => '2',
        "Wednesday" => '3',
        "Thursday"  => '4',
        "Friday"    => '5',
        "Saturday"  => '6',
    );
    $user_data_tmp = array();
    $now_day = isset($day_conf[$now_day])?$now_day:$day_cn_conf[date("l")];
    $user_item_list = array();


    $field_code_conf = array(
        "B" => '會前禱告',
        "C" => '司會',
        "D" => '敬拜主領',
        "E" => '配唱',
        "F" => '配唱',
        "G" => '配唱',
        "H" => '司琴',
        "I" => '司鼓',
        "J" => '視聽',
        "K" => '視聽',
        "L" => '司獻',
        "M" => '司獻',
        "P" => '司獻',
        "P" => '小組破冰',
        "Q" => '小組詩歌',
        "R" => '信息複習',
    );

    $alphabet = range('A', 'Z');
    $field_num_conf = array();
    foreach ($field_code_conf as $key => $service_item) {
        $field_num = array_search($key, $alphabet);
        $field_num_conf[$field_num] = $service_item;
    }

    foreach ($nex_data as $key => $name) {
        $user_data = getUserInfoForName($name);

        if(empty($user_data)){
            continue;
        }

        $user_id = $user_data['user_id'];

        if(isset($field_num_conf[$key])){
            $user_item_list[$user_id][] = $field_num_conf[$key];
        }

    }

    echo '<pre>';
    print_r($user_item_list);
    echo '</pre>';
    exit();
    foreach ($user_item_list as $user_id => $items) {
        $user_data = $db->getSingleByArray('sheet_notify_user',array(
            'id'     => $user_id,
            'notify_day'    => $now_day,
            'enable_notify' => 1
        ));
        if(empty($user_data)){
            continue;
        }
        $items_str = implode('、',$items);
        $msg = $user_data['real_name']." 平安！\n您這週六($next_day)有".$items_str."的服事，請預備心服事，願神祝福您。".emoji("10008D");

        $result = $client->toyMessage($user_data['line_user_uuid'],$msg);
        $db->insertData("sheet_notify_notify_log",array(
            "line_user_uuid" => $user_data['line_user_uuid'],
            "type"           => "notify",
            "real_name"      => $user_data['real_name'],
            "msg"            => $msg,
            "response"       => $result['msg'],
            "status"         => $result['status'],
            "create_time"    => date('Y-m-d H:i:s')
        ));
    }

    function emoji($code){
        $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code);
        $emoticon =  mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
        return $emoticon;
    }

    function getUserInfoForName($name){
        global $user_data_tmp,$db;
        if(!isset($user_data_tmp[$name])){
            $data = $db->getSingleById('sheet_notify_user_sheet_names','name',$name);
            $user_data_tmp[$name] = $data;
        }else{
            $data = $user_data_tmp[$name];
        }
        return $data;
    }
