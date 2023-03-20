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
        http://128.199.189.93/gsheet_work_notify/
        $user_data = $db->getSingleById('sheet_notify_user','id',$user_id);
    }else{
        $user_id = $user_data['id'];
    }
    $change_week_day_cn_conf = array(
        '0' => "日",
        '1' => "一",
        '2' => "二",
        '3' => "三",
        '4' => "四",
        '5' => "五",
        '6' => "六",
    );
    $notify_user_name = ($user_data['real_name']!='')?$user_data['real_name']:$user_data['line_name'];
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
            $str_arr = explode(":",$text);
            $action  = isset($str_arr[0])?$str_arr[0]:'';
            $val     = isset($str_arr[1])?$str_arr[1]:'';

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
            if($event['type']=='follow'){
                $send_msg = $notify_user_name."平安！ 我是您的服事提醒小天使，我會每週提醒您當週的服事！\n但請注意，有時候我的資訊會錯誤或是睡過頭忘記了XD，\n所以還是請您以教會週報、群組服事表為主呦！\n\n操作方式為點選下方選單日~六設定通知日，若不想接收通知可點選「關閉提醒」。\n\n";
                if($user_data['enable_notify']==0){
                    $send_msg .= '目前為關閉提醒';
                }else{
                    $send_msg .= '目前提醒時間為每週'.$change_week_day_cn_conf[$user_data['notify_day']];
                }

                $result = $client->reply_text($event['replyToken'],$send_msg."\n\nBy the way ，若您是剛加入我好友，我還不認識你，所以功能應該是無法使用XD\n還請等待後台管理者綁定帳號，或主動通知管理者協助綁定~");

            }

            break;
        case 'postback':
            $send_msg = '';
            $result = array();
            $message = $event['postback']['data'];
            switch ($message) {
                case 'set0':
                case 'set1':
                case 'set2':
                case 'set3':
                case 'set4':
                case 'set5':
                case 'set6':
                    $day  = str_replace('set','',$message);
                    $update_data = array(
                        "modify_time"   => date('Y-m-d H:i:s'),
                        'notify_day'    => $day,
                        'enable_notify' => 1
                    );

                    $result = $db->updateData("sheet_notify_user",$update_data,array("id"=>$user_id));
                    if($result){
                        $send_msg = "收到！我將會在每週".$change_week_day_cn_conf[$day]."提醒".$notify_user_name."當週的服事！";
                    }else{
                        $send_msg = "設定提醒失敗惹。QQ";
                    }
                    $result = $client->reply_text($event['replyToken'],$send_msg);

                    break;
                case 'off':
                    $update_data = array(
                        "modify_time" => date('Y-m-d H:i:s'),
                        'enable_notify'  => '0'
                    );
                    $result = $db->updateData("sheet_notify_user",$update_data,array("id"=>$user_id));
                    if($result){
                        $send_msg = "好的！我將不會再打擾".$notify_user_name."了，如果想再找我幫忙，可以點選下方選單的提醒日，將會重新開啟提醒呦~";
                    }else{
                        $send_msg = "關閉提醒失敗惹。QQ";
                    }
                    $result = $client->reply_text($event['replyToken'],$send_msg);

                case 'all':
                    $spreadsheet_id   = $db->getConfigValue('sheet_code');
                    $user_list_result = $db->getAllSheetUser($user_data['line_user_uuid']);
                    $user_list        = isset($user_list_result['data'])?$user_list_result['data']:array();
                    $now_year = date("Y");
                    $sheet_list       = $db->getGoogleSheet($spreadsheet_id,$now_year);
                    $work_field_conf = array(
                        "1"  => "會前禱告",
                        "2"  => "司會",
                        "3"  => "敬拜主領",
                        "4"  => "配唱",
                        "5"  => "配唱",
                        "6"  => "配唱",
                        "7"  => "司琴",
                        "8"  => "司鼓",
                        "9"  => "視聽",
                        "10"  => "視聽",
                        "11" => "司獻",
                        "12" => "司獻",
                        "13" => "破冰",
                        "14" => "詩歌",
                        "15" => '信息複習',
                        "16" => '小組晚餐預備',
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

                    $msg = $notify_user_name."平安！ 以下是您這季接下來的服事，請預備心呦~\n";
                    $work_sheet = array();
                    foreach ($work_list as $work_date => $work_val) {
                        $work_sheet[] = $work_date."  ". implode('、',$work_val);
                    }
                    $msg .= implode("\n",$work_sheet);
                    $send_msg = $msg;
                    $result = $client->reply_text($event['replyToken'],$send_msg);
                    break;
                case 'instruction':
                    $send_msg = "嗨 ".$notify_user_name."~ 您可以點選下方選單日~六設定通知日，若不想接收通知請點選「關閉提醒」。\n\n";
                    if($user_data['enable_notify']==0){
                        $send_msg .= '目前為關閉提醒';
                    }else{
                        $send_msg .= '目前提醒時間為每週'.$change_week_day_cn_conf[$user_data['notify_day']];
                    }

                    $result = $client->reply_text($event['replyToken'],$send_msg);
                    break;
            }
            $db->insertData("sheet_notify_notify_log",array(
                "line_user_uuid" => $user_data['line_user_uuid'],
                "type"           => "set",
                "real_name"      => $user_data['real_name'],
                "msg"            => $send_msg,
                "response"       => isset($result['msg'])?$result['msg']:'',
                "status"         => isset($result['status'])?$result['status']:'',
                "create_time"    => date('Y-m-d H:i:s')
            ));
            break;
        default:
            error_log('Unsupported event type: ' . $event['type']);
            break;
    }
}
