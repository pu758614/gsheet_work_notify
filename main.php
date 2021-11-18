<?php
    require 'vendor/autoload.php';
    include_once("lib/lib.php");

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $db = new db_lib();

    $spreadsheet_id = isset($_ENV['SPREADSHEET_ID'])?$_ENV['SPREADSHEET_ID']:'';
    $now_day        = isset($_ENV['NOW_DAY'])?$_ENV['NOW_DAY']:'';
    $list_data      = $db->getGoogleSheet($spreadsheet_id);

    $next_day = date("m/d",strtotime("next Saturday"));
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
    $now_day = isset($day_conf[$now_day])?$day_conf[$now_day]:date("l");

    $user_item_list = array();
    foreach ($nex_data as $key => $name) {
        switch ($key) {
            case '1':
                $user_item_list[$name][] = '會前禱告';
                break;
            case '2':
                $user_item_list[$name][] = '司會';
                break;
            case '3':
                $user_item_list[$name][] = '敬拜主領';
                break;
            case '4':
            case '5':
                $user_item_list[$name][] = '配唱';
                break;
            case '6':
                $user_item_list[$name][] = '司琴';
                break;
            case '7':
                $user_item_list[$name][] = '司鼓';
                break;
            case '8':
            case '9':
                $user_item_list[$name][] = '視聽';
                break;
            case '10':
            case '11':
                $user_item_list[$name][] = '司獻';
                break;
            case '14':
                $user_item_list[$name][] = '小組破冰';
                break;
            case '15':
                $user_item_list[$name][] = '小組詩歌';
                break;
        }
    }
    foreach ($user_item_list as $user_name => $items) {

        $user_data = $db->getSingleByArray('sheet_notify_user',array(
            'real_name'  => $user_name,
            'notify_day' => $now_day
        ));
        if(empty($user_data)){
            continue;
        }
        $items_str = implode('、',$items);
        $msg = $user_name."平安  這週有".$items_str."的服事。";
        echo $msg;
        $notify_result = $db->notifyAction($user_data['line_user_uuid'],$msg);
    }


    //print_r($user_item_list);