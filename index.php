<?php
    require 'vendor/autoload.php';
    include_once("lib/lib.php");
    include_once("lib/TemplatePower/class.TemplatePower.inc.php");

    $db = new db_lib;
    $tpl_path = "tpl/index.tpl";
    $tpl = new TemplatePower ($tpl_path);
    session_start();
    $action = isset($_GET['action'])?$_GET['action']:'basic_info';

    $tpl->assignGlobal(array(
        "action" => $action,
       // 'host'   => $host
    ));

    $tpl -> prepare ();
    $tpl_path = "tpl/".$action.".tpl";
    $tpl->assignInclude( "content", $tpl_path );
    $tpl -> prepare ();
    if(is_file($action.".php")){
        include($action.".php");
    }


    $tpl -> printToScreen ();
