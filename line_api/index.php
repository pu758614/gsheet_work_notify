<?php
header("Content-Type:text/html; charset=utf-8");

include ('../vendor/autoload.php');
include ('../lib/lib.php');
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
    error_log("AAAtest  userId:". $line_uuid,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if(empty($user_data)){
        $profile = $client->getGuestInfo($line_uuid);
        $user_id = $db->insertData('sheet_notify_user',array(
            'line_user_uuid' => $line_uuid,
            'real_name'      => isset($profile['displayName'])?$profile['displayName']:'',
            'line_name'      => isset($profile['displayName'])?$profile['displayName']:'',
            'modify_time'    => date('Y-m-d H:i:s'),
            'create_time'    => date('Y-m-d H:i:s'),
        ));
        error_log("BBBtest  userId:". $user_id,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $user_data = $db->getSingleById('sheet_notify_user','id',$user_id);
    }else{
        $user_id = $user_data['id'];
        error_log("CCCtest  userId:". $user_id,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    error_log('Unsupported event type: ' . $event['type']);
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            if($message['type']!='text'){
                continue;
            }
            $str     = $message['text'];
            $str     = strtolower($str);
            $str     = convertStrType($str);
            $str     = str_replace(' ','',$str);
            $str_arr = explode(":",$str);
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
            if(count($str_arr)==2 && $action=='set'){
                $update_data = array(
                    "modify_time" => date('Y-m-d H:i:s')
                );
                if(isset($change_week_day_cn_conf[$val])){
                    $update_data['notify_day'] = $val;
                    $msg = "您的提醒時間為每週".$change_week_day_cn_conf[$val];
                }else if($val=='on'){
                    $update_data['enable_notify'] = 1;
                    $msg = "已經啟動提醒，提醒時間為每週".$change_week_day_cn_conf[$user_data['notify_day']];
                }else if($val=='off'){
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
                }
            }

            if($message['text']=='三民聖教會'){
                $client->replyMessage([
                    'replyToken' => $event['replyToken'],
                    'messages' => [
                        [
                            "type"=>"location",
                            "title"=>"灣告輝底家啦！！",
                            "address"=>"813左營區重立路61號",
                            "latitude"=>'22.673217',
                            "longitude"=>'120.313176'
                        ]
                    ]
                ]);
                break;
            }
            break;
        case 'unfollow':
            $data = array(
                "relation"=>"unfollow",
                "modify_time" => date('Y-m-d H:i:s')
            );
            $db->updateData("sheet_notify_user",$data,array("id"=>$user_id));
            break;
        default:
            error_log('Unsupported event type: ' . $event['type']);
            break;
    }
}

function convertStrType($str) {
    $dbc = array(
        '０' , '１' , '２' , '３' , '４' ,
        '５' , '６' , '７' , '８' , '９' ,
        'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,
        'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,
        'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,
        'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,
        'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,
        'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,
        'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,
        'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,
        'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,
        'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,
        'ｙ' , 'ｚ' , '－' , '　' , '：' ,
        '．' , '，' , '／' , '％' , '＃' ,
        '！' , '＠' , '＆' , '（' , '）' ,
        '＜' , '＞' , '＂' , '＇' , '？' ,
        '［' , '］' , '｛' , '｝' , '＼' ,
        '｜' , '＋' , '＝' , '＿' , '＾' ,
        '￥' , '￣' , '｀'
    );
    $sbc = array( //半形
        '0', '1', '2', '3', '4',
        '5', '6', '7', '8', '9',
        'A', 'B', 'C', 'D', 'E',
        'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y',
        'Z', 'a', 'b', 'c', 'd',
        'e', 'f', 'g', 'h', 'i',
        'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x',
        'y', 'z', '-', ' ', ':',
        '.', ',', '/', '%', ' #',
        '!', '@', '&', '(', ')',
        '<', '>', '"', '\'','?',
        '[', ']', '{', '}', '\\',
        '|', ' ', '=', '_', '^',
        '￥','~', '`'
    );
    return str_replace( $dbc, $sbc, $str ); //全形到半形
}
