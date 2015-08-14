<?php
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 10/08/2015
 * Time: 18:43
 */

include('../../database/connection.php');
include('../../request/Message.php');
include('User.php');

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
    case 1:
        $bdd = ConnectToMySQL();
        if($parse_url[0][0] == 'pseudo'){
            if(User::isPseudoTaken($bdd, $parse_url[0][1])){
                Message::sendJSONMessage(true, "Le nom d'utilisateur est déjà pris");
            }else{
                Message::sendJSONMessage(false, "Pseudo libre");
            }
        }else if($parse_url[0][0] == 'email'){
            if(User::isEmailTaken($bdd, $parse_url[0][1])){
                Message::sendJSONMessage(true, "Le mail est déjà pris");
            }else{
                Message::sendJSONMessage(false, "Mail libre");
            };
        }else{
            Message::sendJSONMessage(true, "Erreur serveur");
        }
        break;
    default:
        Message::sendJSONMessage(true, "Erreur serveur");
        break;
}

function CreateUser($pseudo, $pwd, $email){
    $new_user = new User($pseudo, $pwd, $email);
    $new_user->addToDatabase();
}

?>