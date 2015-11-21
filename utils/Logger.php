<?php

/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 21/11/2015
 * Time: 12:17
 */
class Logger{
    public static $file = "fuel/log/log.log";

    public static function debugLog($message){
        $data = "LOG DEBUG - From ".$_SERVER['REMOTE_ADDR']." | ".$message."\n";
        self::writeLog($data);
    }

    private static function writeLog($message){
        file_put_contents( ($_SERVER["DOCUMENT_ROOT"].self::$file), $message, FILE_APPEND | LOCK_EX);
    }
}