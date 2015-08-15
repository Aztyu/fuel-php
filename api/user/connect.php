<?php
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 12/08/2015
 * Time: 22:48
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
    case 2:
        $bdd = ConnectToMySQL();
        if($parse_url[0][0] == 'pseudo' && $parse_url[1][0] == 'password'){
            $user = User::getUser($bdd, $parse_url[1][1], $parse_url[0][1], null);
            if($user){
                Message::sendJSONMessage(false, $user->asArray());
            }else{
                Message::sendJSONMessage(true, "Nom d'utilisateur ou mot de passe invalide");
            }
        }else if($parse_url[0][0] == 'email' && $parse_url[1][0] == 'password'){
            $user = User::getUser($bdd, $parse_url[0][1], null, $parse_url[0][1]);
            if($user){
                Message::sendJSONMessage(false, $user->asArray());
            }else{
                Message::sendJSONMessage(true, "Nom d'utilisateur ou mot de passe invalide");
            }
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