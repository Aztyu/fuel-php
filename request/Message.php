<?php
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 10/08/2015
 * Time: 18:52
 */

class Message {
    public static function sendJSONMessage($error, $message){
        $json_data = array(
            'result' => ($error?"error":"success"),
            'item' => $message
        );
        echo json_encode($json_data, JSON_UNESCAPED_UNICODE);
    }
}