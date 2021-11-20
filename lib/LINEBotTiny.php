<?php


if (!function_exists('hash_equals')) {
    defined('USE_MB_STRING') or define('USE_MB_STRING', function_exists('mb_strlen'));

    function hash_equals($knownString, $userString)
    {
        $strlen = function ($string) {
            if (USE_MB_STRING) {
                return mb_strlen($string, '8bit');
            }

            return strlen($string);
        };

        // Compare string lengths
        if (($length = $strlen($knownString)) !== $strlen($userString)) {
            return false;
        }

        $diff = 0;

        // Calculate differences
        for ($i = 0; $i < $length; $i++) {
            $diff |= ord($knownString[$i]) ^ ord($userString[$i]);
        }
        return $diff === 0;
    }
}

class LINEBotTiny
{
    private $channelAccessToken;
    private $channelSecret;

    public function __construct($channelAccessToken, $channelSecret)
    {
        $this->channelAccessToken = $channelAccessToken;
        $this->channelSecret = $channelSecret;
    }

    public function parseEvents()
    {
        // if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        //     http_response_code(405);
        //     error_log('Method not allowed');
        //     exit();
        // }

        $entityBody = file_get_contents('php://input');

        // if (strlen($entityBody) === 0) {
        //     http_response_code(400);
        //     error_log('Missing request body');
        //     exit();
        // }
        //
        // if (!hash_equals($this->sign($entityBody), $_SERVER['HTTP_X_LINE_SIGNATURE'])) {
        //     http_response_code(400);
        //     error_log('Invalid signature value');
        //     exit();
        // }
        //
        $data = json_decode($entityBody, true);
        // if (!isset($data['events'])) {
        //     http_response_code(400);
        //     error_log('Invalid request body: missing events property');
        //     exit();
        // }
        return $data['events'];
    }

    // public function reply_text_to($user_id,$text=''){
    //     $this->toyMessage(array(
    //         'to' => $user_id,
    //         'messages' => array(
    //             array(
    //                 'type' => 'text',
    //                 'text' => $text
    //             ),
    //         )
    //     ));
    // }
    public function toyMessage($user_id,$message) {
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($this->channelAccessToken);
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $this->channelSecret]);

        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
        $response = $bot->pushMessage($user_id, $textMessageBuilder);
        if ($response->getHTTPStatus() !='200') {
            error_log('Request failed: '. $response->getHTTPStatus() . ' ' . $response->getRawBody());
        }
    }




    public function reply_text($replyToken,$text=''){
        $this->replyMessage(array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $text
                ),
            )
        ));
    }
    public function reply_text_arr($replyToken,$text_arr = array()){
        $count = 0;
        foreach ($text_arr as $key => $text) {
            $message_arr[$count]['type'] = 'text';
            $message_arr[$count]['text'] = $text;
            $count++;
        }

        $this->replyMessage(array(
            'replyToken' => $replyToken,
            'messages' => $message_arr
        ));
    }


    public function replyMessage($message)
    {
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->channelAccessToken,
        );

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $header),
                'content' => json_encode($message),
            ],
        ]);

        $response = file_get_contents('https://api.line.me/v2/bot/message/reply', false, $context);
        if (strpos($http_response_header[0], '200') === false) {
            http_response_code(500);
            error_log('Request failed: ' . $response);
        }
    }

    function getGuestInfo($user_id){
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($this->channelAccessToken);
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $this->channelSecret]);
        $response = $bot->getProfile($user_id);
        $profile = array();
        if ($response->isSucceeded()) {
            $profile = $response->getJSONDecodedBody();
        }
        return $profile;
    }

    private function sign($body)
    {
        $hash = hash_hmac('sha256', $body, $this->channelSecret, true);
        $signature = base64_encode($hash);
        return $signature;
    }
}