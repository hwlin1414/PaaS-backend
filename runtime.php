<?php

include_once "include_check.php";
include_once "include_command.php";
include_once "include_func.php";

if(!isset($_POST['name']) || !check_name($_POST['name'])){
    die(JSON_encode(['msg' => "POST['name'] check failed"]));
}

$lines = run("{$sudo} {$rctl} -hu jail:{$_POST['name']}");
if($ret === FALSE){
    die(JSON_encode(['msg' => "command failed"]));
}

$lines = explode("\n", $lines);

$return = [];
foreach($lines as $line){
    if($line == "") continue;

    $kv = explode("=", $line, 2);
    $return[$kv[0]] = $kv[1];
}

echo JSON_encode($return);
