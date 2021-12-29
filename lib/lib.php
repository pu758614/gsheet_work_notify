<?php
    include_once("db_crud.php");
    class  db_lib {
        use DB_CRUD\DB_CRUD;
        function __construct(){
            date_default_timezone_set('asia/taipei');
            header("Content-type: text/html; charset=utf-8");
            if (file_exists( '.env')) {
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

        function getNotifyRecordList(){



            $sql = "SELECT *
                    FROM   sheet_notify_notify_log";
            $rs = $this->db->Execute($sql);
            if($rs && $rs->RecordCount() > 0){
                return $rs->getAll();
            }else{
            }

        }


        function getAllSheetUser($uuid){
            $user_data = $this->getSingleById('sheet_notify_user','line_user_uuid',$uuid);
            if(empty($user_data)){
                return array(
                    'error' => true,
                    'msg' => '錯誤的user_uuid'
                );
            }
            $sheet_names = $this->getArrayById('sheet_notify_user_sheet_names','user_id',$user_data['id']);
            $name_list = array();
            foreach ($sheet_names as $key => $sheet_name) {
                $name_list[] = $sheet_name['name'];
            }
            return array(
                'error' => false,
                'msg'   => '錯誤的user_uuid',
                'data'  => $name_list
            );
        }

        function getConfigValue($item){
            $data = $this->getSingleById('sheet_notify_config','item',$item);
            $value = isset($data['value'])?$data['value']:'';
            return $value;
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
                "private_key"  => isset($_ENV['GOOGLE_SHEET_PRIVATE_KEY'])?$_ENV['GOOGLE_SHEET_PRIVATE_KEY']:'',
            );
            $client->setAuthConfig($config);
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
                error_log("get sheet error.id: $id response:".$e->getMessage(),JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            return $data;
        }

        function getUserList(){
            $tb = 'sheet_notify_user';
            $where = '';
            if($where!=''){
                $where = 'WHERE '.$where;
            }

            $sql = "SELECT * FROM $tb  $where";

            $rs =  $this->db->Execute($sql);

            if($rs && $rs->RecordCount() > 0){
                return $rs->getAll();
            }else{
                return array();
            }
        }

        function checkNameExist($id,$name){
            $tb = 'sheet_notify_user_sheet_names';
            $sql = "SELECT user_id
                    FROM   $tb
                    WHERE  user_id != ? AND name=?";
            $rs = $this->db->Execute($sql,array($id,$name));
            if($rs && $rs->RecordCount() > 0){
                return true;
            }else{
                return false;
            }
        }
    }
