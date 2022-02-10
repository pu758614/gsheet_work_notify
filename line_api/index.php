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
            $text = $message['text'];
            $text = strtolower($text);
            $text = convertStrType($text);
            $text = str_replace(' ','',$text);
            switch ($text) {
                case 'all':
                    $spreadsheet_id   = $db->getConfigValue('sheet_code');
                    $user_list_result = $db->getAllSheetUser($user_data['line_user_uuid']);
                    $user_list        = isset($user_list_result['data'])?$user_list_result['data']:array();
                    $sheet_list       = $db->getGoogleSheet($spreadsheet_id);
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
                        if(strtotime($sheet_data[0])>= strtotime(date('m/d'))){
                            foreach ($sheet_data as $sheet_key => $sheet_val) {
                                if(in_array($sheet_val,$user_list)){
                                    $work_name = isset($work_field_conf[$sheet_key])?$work_field_conf[$sheet_key]:'';
                                    $work_list[$date][] = $work_name;
                                }
                            }
                        }
                    }

                    $msg = $user_data['real_name']."平安！ 以下是您這季接下來的服事，請預備心呦~\n";
                    foreach ($work_list as $work_date => $work_val) {
                        $msg .= $work_date."  ". implode('、',$work_val)."\n";
                    }

                    $result = $client->reply_text($event['replyToken'],$msg);
                    break;

                default:
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
        case 'postback':
            $result = $client->reply_text($event['replyToken'],json_encode($event,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            break;
        default:
            error_log('Unsupported event type: ' . $event['type']);
            break;
    }
}
