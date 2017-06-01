<?php

include_once "include_command.php";
include_once "include_func.php";

$lines = run("{$ezjail_admin} list");
$zfs  = run("{$zfs} list -Hp | grep zroot/usr/jails/");

$lines = explode("\n", $lines);
unset($lines[0]);
unset($lines[1]);
$zfs = explode("\n", $zfs);

$return = [];
foreach($lines as $line){
    if($line != ""){
        $column = preg_split('/\s+/', $line);
        $used = [0, 0];
        foreach($zfs as $z){
            $zcol = preg_split('/\s+/', $z);
            if(strstr($zcol[0], $column[3])){
                $used = [$zcol[1], $zcol[2]];
            }
        }

        $return[] = [
            'flags' => $column[0],
            'running' => ($column[1] != "N/A"),
            'IP' => $column[2],
            'name' => $column[3],
            'used' => $used[0],
            'avail' => $used[1],
        ];
    }
}

echo JSON_encode($return);
