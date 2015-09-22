<?php
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 05/08/2015
 * Time: 19:47
 */

include('../../database/connection.php');
include('../../request/Message.php');
include('../../entities/User.php');

header ('Content-type: text/html; charset=utf-8');

$parse_url = array();

$arg_count = 0;

foreach($_REQUEST as $key => $value){
    $buffer = explode("=", $value);
    $parse_url[$arg_count][0] = $key;
    $parse_url[$arg_count][1] = $value;
    $arg_count++;
}

switch(count($parse_url)){
    case 3:
        if($parse_url[0][0] == 'pseudo' && $parse_url[1][0] == 'password' && $parse_url[2][0] == 'email'){
            CreateUser($parse_url[0][1], $parse_url[1][1], $parse_url[2][1]);
        }
        break;
    default:
        Message::sendJSONMessage(false, "Requete invalide");
        break;
}

function CreateUser($pseudo, $pwd, $email){
    $new_user = new User($pseudo, $pwd, $email);
    $new_user->addToDatabase();
}

?>