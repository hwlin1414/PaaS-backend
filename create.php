<?php

include_once "include_check.php";
include_once "include_command.php";
include_once "include_func.php";
$sshkey_header = "command=\"/usr/local/bin/sudo /usr/local/bin/ezjail-admin console {$_POST['name']}\",no-port-forwarding,no-X11-forwarding,no-agent-forwarding";

//check
if(!isset($_POST['name']) || !check_name($_POST['name'])){
    die(JSON_encode(['msg' => "POST['name'] check failed"]));
}

if(!isset($_POST['ip']) || !check_ip($_POST['ip'])){
    die(JSON_encode(['msg' => "POST['ip'] check failed"]));
}

if(!isset($_POST['size'])) $_POST['size'] = "4G";
if(!check_size($_POST['size'])){
    die(JSON_encode(['msg' => "POST['size'] check failed"]));
}



//ezjail create
if(run("{$sudo} {$ezjail_admin} create {$_POST['name']} {$_POST['ip']}") === FALSE){
    die(JSON_encode(['msg' => "ezjail-admin create failed"]));
}
//ezjail raw_socket
if(run("{$sudo} {$sysrc} -f /usr/local/etc/ezjail/{$_POST['name']} \"export jail_{$_POST['name']}_parameters=allow.raw_sockets=1\"") === FALSE){
    die(JSON_encode(['msg' => "write ezjail config failed"]));
}
//zfs
if(run("{$sudo} {$zfs} set quota={$_POST['size']} zroot/usr/jails/{$_POST['name']}") === FALSE){
    die(JSON_encode(['msg' => "zfs size failed"]));
}
//rctl
if(run("{$sudo} {$rctl} -a jail:{$_POST['name']}:memoryuse:deny=512M") === FALSE){
    die(JSON_encode(['msg' => "rctl 1 failed"]));
}
if(run("{$sudo} {$rctl} -a jail:{$_POST['name']}:nthr:deny=128") === FALSE){
    die(JSON_encode(['msg' => "rctl 2 failed"]));
}
if(run("{$sudo} {$rctl} -a jail:{$_POST['name']}:pcpu:deny=100") === FALSE){
    die(JSON_encode(['msg' => "rctl 3 failed"]));
}
//rctl-startup
if(run("{$sudo} {$sysrc} -f /etc/rctl.conf jail:{$_POST['name']}:memoryuse:deny=512M") === FALSE){
    die(JSON_encode(['msg' => "rctl-startup 1 failed"]));
}
if(run("{$sudo} {$sysrc} -f /etc/rctl.conf jail:{$_POST['name']}:nthr:deny=128") === FALSE){
    die(JSON_encode(['msg' => "rctl-startup 2 failed"]));
}
if(run("{$sudo} {$sysrc} -f /etc/rctl.conf jail:{$_POST['name']}:pcpu:deny=100") === FALSE){
    die(JSON_encode(['msg' => "rctl-startup 3 failed"]));
}
//ssh-key
$fp = fopen("key/{$_POST['name']}.pub", "w");
fwrite($fp, "{$sshkey_header} {$_POST['key']}\n");
fclose($fp);
if(run("{$sudo} {$keygen_sh}") === FALSE){
    die(JSON_encode(['msg' => "ssh-key failed"]));
}
