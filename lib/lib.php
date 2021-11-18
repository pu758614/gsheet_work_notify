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
            echo "1<br>";
            $client = new \Google_Client();
            echo "2<br>";
            $client->setApplicationName('Google Sheets and PHP');
            echo "3<br>";
            // 設定權限
            $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
            echo "4<br>";
            $client->setAccessType('offline');
            echo "5<br>";
            // 引入金鑰
            $config = array(
                "type"         => isset($_ENV['GOOGLE_SHEET_TYPE'])?$_ENV['GOOGLE_SHEET_TYPE']:'',
                "client_id"    => isset($_ENV['GOOGLE_SHEET_CLIENT_ID'])?$_ENV['GOOGLE_SHEET_CLIENT_ID']:'',
                "client_email" => isset($_ENV['GOOGLE_SHEET_CLIENT_EMAIL'])?$_ENV['GOOGLE_SHEET_CLIENT_EMAIL']:'',
                "private_key"  => isset($_ENV['GOOGLE_SHEET_PRIVATE_KEY'])?$_ENV['GOOGLE_SHEET_PRIVATE_KEY']:'',
            );
            $client->setAuthConfig($config);
            echo "6<br>";

            // 建立 Google Sheets Service
            $service = new \Google_Service_Sheets($client);
            // 取得 Sheet 範圍
            $getRange = "A:P";
            echo "7<br>";
            // 讀取資料
            $data = array();
            try {
                echo "8<br>";
                $response = $service->spreadsheets_values->get($id, $getRange);
                $data = $response->getValues();
                echo "9<br>";
            } catch (Exception $e) {
                print json_encode($e->getMessage(),JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                echo "10<br>";
            }
            return $data;
        }
    }
