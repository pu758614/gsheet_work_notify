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

$client = new LINEBotTiny($channel_access_token, $channel_secret);
foreach ($client->parseEvents() as $event) {
    $user_id = $event['source']['userId'];
    error_log('Unsupported event type: ' . $event['type']);
    //$guestdata = getGuestInfo($channelAccessToken,$channelSecret,$user_id);

};