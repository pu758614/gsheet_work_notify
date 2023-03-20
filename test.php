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
$field_code_conf = array(
    "B" => '會前禱告',
    "C" => '司會',
    "D" => '敬拜主領',
    "E" => '配唱',
    "F" => '配唱',
    "G" => '配唱',
    "H" => '司琴',
    "I" => '司鼓',
    "J" => '視聽',
    "K" => '視聽',
    "L" => '司獻',
    "M" => '司獻',
    "P" => '司獻',
    "P" => '小組破冰',
    "Q" => '小組詩歌',
    "R" => '信息複習',
    "S" => '小組晚餐預備',
);
$alphabet = range('A', 'Z');
$field_num_conf = array();
foreach ($field_code_conf as $key => $service_item) {
    $field_num = array_search($key, $alphabet);
    $field_num_conf[$field_num] = $service_item;
}
echo '<pre>';
print_r($field_num_conf);
echo '</pre>';
