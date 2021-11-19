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
    if(empty($user_data)){
        $profile = $client->getGuestInfo($line_uuid);
        $user_id = $db->insertData('sheet_notify_user',array(
            'line_user_uuid' => $line_uuid,
            'line_name'      => isset($profile['displayName'])?$profile['displayName']:'',
            'modify_time'    => date('Y-m-d H:i:s'),
            'create_time'    => date('Y-m-d H:i:s'),
        ));
    }else{
        $user_id = $user_data['id'];
    }
    $client->toyMessage($line_uuid,"YOYOYOYO");
}
//     switch ($event['type']) {
//         case 'follow':

//             break;
//         case 'message':
//             $message = $event['message'];
//             $client->reply_text($event['replyToken'],$message['type']);
//             // switch ($message['type']) {
//             //     case 'value':
//             //         # code...
//             //         break;
//             //     case 'text':
//             //         $client->reply_text($event['replyToken'],"123456");
//             //         // if($message['text']=='三民聖教會'){

//             //             // $client->replyMessage([
//             //             //     'replyToken' => $event['replyToken'],
//             //             //     'messages' => [
//             //             //         [
//             //             //             "type"=>"location",
//             //             //             "title"=>"灣告輝底家啦！！",
//             //             //             "address"=>"813左營區重立路61號",
//             //             //             "latitude"=>'22.673217',
//             //             //             "longitude"=>'120.313176'
//             //             //         ]
//             //             //     ]
//             //             // ]);
//             //         //     write_log($db,$guestdata['displayName'],$user_id,$message['text'],'4');
//             //         //     break;
//             //         // }

//             //         // $comman_key =array('?','這到底怎麼用啦','目錄','舊約','新約','我要抽');
//             //         // if(in_array($message['text'],$comman_key)){
//             //         //     write_log($db,$guestdata['displayName'],$user_id,'comman-'.$message['text'],'4');
//             //         //     exit;
//             //         // }
//             //         // $status = 0;

//             //         // $data = cheack_arrange($message['text'],$user_id);
//             //         // if($data['error'] == '1'){
//             //         //     $client->reply_text($event['replyToken'],$data['msg']);
//             //         //     write_log($db,$guestdata['displayName'],$user_id,$message['text'],'0');
//             //         //     exit;
//             //         // }
//             //         // if($data['type'] == 'search' ){

//             //         //     $data['sec'] = isset($data['sec'])?$data['sec']:'';
//             //         //     $results = search($data['book'],$data['chap'],$data['sec'],$message['text']);

//             //         //     if($results['error']!='1' && $results['status']=='1'){
//             //         //         $text_arr = text_change_arr($results['data']);
//             //         //         $client->reply_text_arr($event['replyToken'],$text_arr);
//             //         //     }else if($results['error']!='1' && $results['status']=='2'){
//             //         //         $client->reply_text($event['replyToken'],$results['data']);
//             //         //     }
//             //         //     $status = $results['status'];
//             //         // }else if($data['type'] == 'kw' ||$data['type'] == 'kwf'){
//             //         //     $results = search_keyword($data['kw'],$data['type']);
//             //         //     if($results['status']=='ok'){
//             //         //         $status ='1';
//             //         //     }else if($results['status']=='error'){
//             //         //         $status = '6';
//             //         //     }else{
//             //         //         $status = '1';
//             //         //     }
//             //         //         $client->reply_text($event['replyToken'],$results['msg']);
//             //         // }else if($data['type']=='log' && $user_id=='U7024af33ac34455f97b39b7bee8b8436'){
//             //         //     $text = get_log($db,$data['count']);
//             //         //     $client->reply_text($event['replyToken'],$text);
//             //         //     exit;
//             //         //     //write_log($db,$guestdata['displayName'],$user_id,$message['text'],'1');
//             //         // }else{
//             //         //     $text = '意料以外的錯誤，請麻煩通知開發人一下！'.emoji('10007D');
//             //         //     $client->reply_text($event['replyToken'],$text);
//             //         //     $status = '6';
//             //         // }
//             //         // write_log($db,$guestdata['displayName'],$user_id,$message['text'],$status);
//             //         // break;
//             //     default:
//             //         // write_log($db,$guestdata['displayName'],$user_id,'Unsupported message type-'.$message['type'],'5');
//             //          error_log('Unsupported message type: ' . $message['type']);
//             //         break;
//             // }
//             break;
//         default:
//             //write_log($db,$guestdata['displayName'],$event['source']['userId'],'Unsupported event type-'.$event['type'],'5');
//             error_log('Unsupported event type: ' . $event['type']);
//             break;
//     }
 //};