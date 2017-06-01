<?php

include_once "include_check.php";
include_once "include_command.php";
include_once "include_func.php";

if(!isset($_POST['action']) || !check_action($_POST['action'])){
    die(JSON_encode(['msg' => "POST['action'] check failed"]));
}
if(!isset($_POST['name']) || !check_name($_POST['name'])){
    die(JSON_encode(['msg' => "POST['name'] check failed"]));
}

$ret = run("{$sudo} {$ezjail_admin} one{$_POST['action']} {$_POST['name']}");
if($ret === FALSE){
    die(JSON_encode(['msg' => "command failed"]));
}
