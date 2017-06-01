<?php

function check_action($var){
    $allow = ['start', 'stop', 'restart'];
    return in_array($var, $allow);
}

function check_name($var){
    return preg_match('/^[a-zA-Z][a-zA-Z0-9_]{2,23}$/', $var);
}

function check_ip($var){
    return preg_match('/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/', $var);
}

function check_size($var){
    return preg_match('/^([0-9]{1,3})[MmGg]$/', $var);
}
