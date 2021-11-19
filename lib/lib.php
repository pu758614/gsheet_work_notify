<?php
    include_once("db_crud.php");
    class  db_lib {
        use DB_CRUD\DB_CRUD;
        function __construct(){
            date_default_timezone_set('asia/taipei');
            header("Content-type: text/html; charset=utf-8");
            if (file_exists(__DIR__ . '/.env')) {
                $dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/..");
                $dotenv->load();
            }
            $host      = isset($_ENV['DB_HOST'])?$_ENV['DB_HOST']:'';
            $user_name = isset($_ENV['DB_USER'])?$_ENV['DB_USER']:'';
            $psw       = isset($_ENV['DB_PSW'])?$_ENV['DB_PSW']:'';
            $db_name   = isset($_ENV['DB_NAME'])?$_ENV['DB_NAME']:'';
            $this->db = ADONewConnection('mysqli');
            $this->db->Connect($host,$user_name,$psw,$db_name);
            $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
        }

        function notifyAction($uuid,$msg){
            return array(
                'error' => false,
                'msg' => ' '
            );
        }

        function getGoogleSheet($id){
            // 建立 Google Client
            $client = new \Google_Client();
            $client->setApplicationName('Google Sheets and PHP');
            // 設定權限
            $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
            $client->setAccessType('offline');
            // 引入金鑰
            $config = array(
                "type"         => isset($_ENV['GOOGLE_SHEET_TYPE'])?$_ENV['GOOGLE_SHEET_TYPE']:'',
                "client_id"    => isset($_ENV['GOOGLE_SHEET_CLIENT_ID'])?$_ENV['GOOGLE_SHEET_CLIENT_ID']:'',
                "client_email" => isset($_ENV['GOOGLE_SHEET_CLIENT_EMAIL'])?$_ENV['GOOGLE_SHEET_CLIENT_EMAIL']:'',
                "private_key"  => '-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCcap91kE0Fa/+k\nEWYX9EliVe3cSqsD+5lChJ1CSK8WVh/yCMXCCM1+hImfshfnEYVin+9I3dVa/fRx\nR1LU4u5dFQKQhTC0ZWLolMPvKGO7L6m4FJNFfuL5USC3du+7ZLxPjVX+407XTfZv\nBL1b3LOjnXFBB/0sXIfqac1nrepdHSBeNv79fdsTsCGbLbz8S8caXIfXYqsK2y1W\nuIXR+WY4R5KLce7GP7W0n97FArLA5GNDl9Xlo84zM7cfRw4ZoGzhmBTdwy6G1QpL\nV2S3m4jVioYX3XLMT3BwGI1uBKuP8w7VBJQZTs3RE1L67Z3JO+g4IF+Q1wSxQbMY\nsc2iLLz5AgMBAAECggEABODx/ZCdSGOilv9YbfuXfN7VgxMco6yJrpiUj5xVArc3\nupzBr1BHOZCgesfLJVDHdmiq5dzOcm68nLpm5982pFZbDL8swlmttLesJ1XdC3n8\nexKAN/ER2wkn4jUOW+vT0E7mHWPBYieLPdH55oc7Hqmy3j6Fq+gscJgxeRAEgPgy\nvHf8WvXfHTU2DNDmAjY4mJ5rGGvjj+T1MJL6HI5fif5s7q4MV/ssAwD8NQqoWMpF\nknOta7lyxmxShtMYlL1crG5AvJyx4c46aXgImujRXs0ZRMF6LWZ9nItnXxVTJ9Q9\nhx87BywWlAIlQVqCXcHJXOpeMqVjy/feZf9ZZ4floQKBgQDO3s2jG3T0zdFFAZur\nvJvOUIQ5be0tBSA1Yjz3FJluBcv+VMtcHnBKjJ16cLexMrcWHvbNJ7SycaGTONQg\nG5mC1A2BmFg6Tv897LDe+1XLiRCf2HfQPYwY4fGDrajG3LAvRV5D41EkWiqKs/fi\nTNEbsaZF6D4WTHDRLatkUBWaGQKBgQDBkFp2TI07/NkVjElHmnyaDRRoq03jXcD4\nK5DnIyG/Awm7bkYjMOFQDUGIKMpCw29NhagMNsHR1eDbor41eei+NvD7hDuRc/tn\nn4uyX7dfYy3jMpOO8S2iqWe/yPHFp0xowoKuJpp9anua9QmWEcZA/GvXH+vsbGdD\nBle31/ZV4QKBgAWVfAWAEzscZx6uuW38TFRYVglaz0Ec1065lR2yP6X5oBUAYvDc\nnXlVrFaGvl6ZGNoPAehtvvHmIU9hBFDNjeo7IRYzb4Y7ZaZdQjTyodE5pOo7pJhJ\nYQO27Zb5VAnyIQtVmwLIGwOZL3bI/tLr8eUGeY9/glWFwLHUwsCVbM/ZAoGAJhah\ntmWZ5RP8I6FXSh+8JRQtz+rliLgKIMtx2AmxukR+xcMNSh90NqxlGMXuBvUuEbMb\nPkwIF6JefNmpVByJD+T/xn5eumB4OAvNEWyESODbRrnND3Ol5zwuji6cZKhnALZF\nwL8X51XsvLE7Eayttlv1XH+LjRpHt4in+iUk9AECgYEAwx1VxTStp+W3xIvg9yb6\nP54FIO33GLWB9s3GB2Yvs33RwoLIoiqSpqFXdf1L2IYzptJ4LqjG4sArFkgPEln1\nnE6qLl/G5XIiJmaKew2e7RAITGf5FkXTIHOWr27MPOws5tMjOoEDCvXX6weneTZq\nL18FDrpF8tw1BWm9m0lw1lo=\n-----END PRIVATE KEY-----\n',
            );
            $client->setAuthConfig("google_sheet_key.json");
           // $client->setAuthConfig("google_sheet_key.json");

            // 建立 Google Sheets Service
            $service = new \Google_Service_Sheets($client);
            // 取得 Sheet 範圍
            $getRange = "A:P";
            // 讀取資料
            $data = array();
            try {
                $response = $service->spreadsheets_values->get($id, $getRange);
                $data = $response->getValues();
            } catch (Exception $e) {
                print json_encode($e->getMessage(),JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            return $data;
        }
    }
