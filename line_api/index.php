<?php
header("Content-Type:text/html; charset=utf-8");

include ('../vendor/autoload.php');
include ('../lib/lib.php');
include ('../lib/common.php');
include ('../lib/LINEBotTiny.php');
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}


$channel_access_token = isset($_ENV['CHANNEL_ACCESS_TOKEN'])?$_ENV['CHANNEL_ACCESS_TOKEN']:'';
$channel_secret       = isset($_ENV['CHANNEL_SECRET'])?$_ENV['CHANNEL_SECRET']:'';
$db = new db_lib();
$client = new LINEBotTiny($channel_access_token, $channel_secret);
foreach ($client->parseEvents() as $event) {

    $line_uuid = $event['source']['userId'];
    $user_data = $db->getSingleById('sheet_notify_user','line_user_uuid',$line_uuid);
    if(empty($user_data)){
        $profile = $client->getGuestInfo($line_uuid);
        $line_name = isset($profile['displayName'])?$profile['displayName']:'';
        $user_id = $db->insertData('sheet_notify_user',array(
            'line_user_uuid' => $line_uuid,
            'real_name'      => $line_name,
            'line_name'      => $line_name,
            'modify_time'    => date('Y-m-d H:i:s'),
            'create_time'    => date('Y-m-d H:i:s'),
        ));

        $user_data = $db->getSingleById('sheet_notify_user','id',$user_id);
    }else{
        $user_id = $user_data['id'];
    }
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            if($message['type']!='text'){
                continue;
            }
            if($message['text']=='?' || $message['text']=='？'){
                continue;
            }
            $text    = $message['text'];
            $text     = strtolower($text);
            $text     = convertStrType($text);
            $text     = str_replace(' ','',$text);
            $str_arr = explode(":",$text);
            $action  = isset($str_arr[0])?$str_arr[0]:'';
            $val     = isset($str_arr[1])?$str_arr[1]:'';
            $change_week_day_cn_conf = array(
                '0' => "日",
                '1' => "一",
                '2' => "二",
                '3' => "三",
                '4' => "四",
                '5' => "五",
                '6' => "六",
            );
            $update_data = array(
                "modify_time" => date('Y-m-d H:i:s')
            );
            if(count($str_arr)==2 && $action=='set' && isset($change_week_day_cn_conf[$val])){
                $update_data['notify_day'] = $val;
                $msg = "您的提醒時間為每週".$change_week_day_cn_conf[$val];
            }

            if($text=='on'){
                $update_data['enable_notify'] = 1;
                $msg = "已經啟動提醒，提醒時間為每週".$change_week_day_cn_conf[$user_data['notify_day']];
            }else if($text=='off'){
                $update_data['enable_notify'] = 0;
                $msg = "已經關閉提醒";
            }

            if(count($update_data)>=2){
                $result = $db->updateData("sheet_notify_user",$update_data,array("id"=>$user_id));
                $send_msg = '';
                if($result){
                    $send_msg = "設定成功，".$msg."。";
                }else{
                    $send_msg = "設定失敗。";
                }
                $result = $client->reply_text($event['replyToken'],$send_msg);
                $db->insertData("sheet_notify_notify_log",array(
                    "line_user_uuid" => $user_data['line_user_uuid'],
                    "type"           => "set",
                    "real_name"      => $user_data['real_name'],
                    "msg"            => $send_msg,
                    "response"       => $result['msg'],
                    "status"         => $result['status'],
                    "create_time"    => date('Y-m-d H:i:s')
                ));
            }else{
                $send_msg = "錯誤的指令，可以輸入「?」來查看指令說明。";
                $result = $client->reply_text($event['replyToken'],$send_msg);
                $db->insertData("sheet_notify_notify_log",array(
                    "line_user_uuid" => $user_data['line_user_uuid'],
                    "type"           => "error",
                    "real_name"      => $user_data['real_name'],
                    "msg"            => $send_msg,
                    "response"       => $result['msg'],
                    "status"         => $result['status'],
                    "create_time"    => date('Y-m-d H:i:s')
                ));
            }
            break;
        case 'unfollow':
        case 'follow':
            $data = array(
                "relation"    => $event['type'],
                "modify_time" => date('Y-m-d H:i:s')
            );
            $db->updateData("sheet_notify_user",$data,array("id"=>$user_id));
            break;
        default:
            error_log('Unsupported event type: ' . $event['type']);
            break;
    }
}
