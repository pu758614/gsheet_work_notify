<?php

require 'vendor/autoload.php';
include_once("lib/lib.php");
include ('lib/common.php');
$action = isset($_GET['action'])?$_GET['action']:'';
$result = array(
    "error" => true,
    "msg"   => '',
    "data"  => ''
);
$db = new db_lib;
$year = date("Y");
echo $year;
// $spreadsheet_id   = $db->getConfigValue('sheet_code');
$spreadsheet_id = '1YC0ZVH-xyqAypAp47CnWzmTcFZWsyhRZGI7Gc_GEb5I';
$list_data      = $db->getGoogleSheet($spreadsheet_id,$year);
echo '<pre>';
print_r($list_data);
echo '</pre>';