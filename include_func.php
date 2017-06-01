<?php

function run($command){
    $descriptor = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"],
        //2 => ["file", "/tmp/error-output.txt", "a"]
    ];
    
    $ret = -1;
    $strerror = "resource unavailable(proc_open failed)\n";
    $date = date("Y-m-d H:i:s");
    $process = proc_open($command, $descriptor, $pipe);
    if(is_resource($process)){
        $str = stream_get_contents($pipe[1]);
        $strerror = stream_get_contents($pipe[2]);
        fclose($pipe[0]);
        fclose($pipe[1]);
        fclose($pipe[2]);
        $ret = proc_close($process);
    }

    // Log
    $fp = fopen("log.txt", "a");
    fwrite($fp, "[{$date}] return: {$ret} command: '{$command}'\n");
    if($ret != 0){
        fwrite($fp, "------\n");
        fwrite($fp, $strerror);
        fwrite($fp, "------\n");
        fclose($fp);
        return FALSE;
    }
    fclose($fp);
    return $str;
}
