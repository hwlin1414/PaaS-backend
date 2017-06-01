<?php

include_once "include_check.php";
include_once "include_command.php";
include_once "include_func.php";

//check
if(!isset($_POST['name']) || !check_name($_POST['name'])){
    die(JSON_encode(['msg' => "POST['name'] check failed"]));
}


//ezjail delete
if(run("{$sudo} {$ezjail_admin} onestop {$_POST['name']}") === FALSE){
    die(JSON_encode(['msg' => "ezjail-admin stop failed"]));
}
if(run("{$sudo} {$ezjail_admin} delete {$_POST['name']}") === FALSE){
    die(JSON_encode(['msg' => "ezjail-admin delete failed"]));
}
//zfs
if(run("{$sudo} {$zfs} destroy zroot/usr/jails/{$_POST['name']}") === FALSE){
    die(JSON_encode(['msg' => "zfs destroy failed"]));
}
if(run("{$sudo} {$rmdir} /usr/jails/{$_POST['name']}") === FALSE){
    die(JSON_encode(['msg' => "rmdir failed"]));
}
//rctl
if(run("{$sudo} {$rctl} -r jail:{$_POST['name']}") === FALSE){
    die(JSON_encode(['msg' => "rctl failed"]));
}
//rctl-startup
if(run("{$sudo} {$sysrc} -f /etc/rctl.conf -x jail:{$_POST['name']}:memoryuse:deny=512M") === FALSE){
    die(JSON_encode(['msg' => "rctl-startup 1 failed"]));
}
if(run("{$sudo} {$sysrc} -f /etc/rctl.conf -x jail:{$_POST['name']}:nthr:deny=128") === FALSE){
    die(JSON_encode(['msg' => "rctl-startup 2 failed"]));
}
if(run("{$sudo} {$sysrc} -f /etc/rctl.conf -x jail:{$_POST['name']}:pcpu:deny=100") === FALSE){
    die(JSON_encode(['msg' => "rctl-startup 3 failed"]));
}
//ssh-key
unlink("key/{$_POST['name']}.pub");
if(run("{$sudo} {$keygen_sh}") === FALSE){
    die(JSON_encode(['msg' => "ssh-key failed"]));
}
