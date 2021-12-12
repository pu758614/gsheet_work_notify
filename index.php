<?php
    require 'vendor/autoload.php';
    include_once("lib/lib.php");
    include_once("lib/TemplatePower/class.TemplatePower.inc.php");

    $db = new db_lib;
    $action = isset($_GET['action'])?$_GET['action']:'user_set';
    $tpl_path = "tpl/index.tpl";
    $tpl = new TemplatePower ($tpl_path);
    session_start();

    $is_login = isset($_SESSION['login'])?$_SESSION['login']:false;

    if(!$is_login){
        $include_tpl = array(
            "content_page" => "tpl/login.tpl",
        );
        $content_php_file = '';
        goto end;
    }


    $content_tpl_file = 'user_set';
    switch ($action) {
        case 'user_set':
            $content_tpl_file = 'user_set';
            $content_php_file = 'user_set';
            break;
        case 'system_set':
            $content_tpl_file = 'system_set';
            $content_php_file = 'system_set';
            break;
    }


    $include_tpl = array(
        "menu_list"    => "tpl/menu_list.tpl",
        "content_page" => "tpl/".$content_tpl_file.".tpl",
    );



    end:


        foreach($include_tpl as $tpl_key => $tpl_val){
            if(isset($tpl_val[0])){
                $tpl->assignInclude( $tpl_key , $tpl_val);
            }
        }
        $tpl->prepare();
        if(file_exists($content_php_file.".php")){
            $tpl->newBlock('signing_in');
            include($content_php_file.".php");
        }


        $tpl -> printToScreen();
