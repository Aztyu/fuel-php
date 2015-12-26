<?php

/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 26/12/2015
 * Time: 16:37
 */

require_once 'database/connection.php';
require_once 'request/Message.php';
require_once 'entities/User.php';

class ApiUserController extends \Pux\Controller{
    public function checkAction(){
        $bdd = Connection::ConnectToMySQL();
        if(isset($_GET["email"])){
            if(User::isEmailTaken($bdd, $_GET["email"])){
                Message::sendJSONMessage(true, "Le mail est déjà pris");
            }else{
                Message::sendJSONMessage(false, "Mail libre");
            }
        }else if(isset($_GET["pseudo"])){
            if(User::isPseudoTaken($bdd, $_GET["pseudo"])){
                Message::sendJSONMessage(true, "Le nom d'utilisateur est déjà pris");
            }else{
                Message::sendJSONMessage(false, "Pseudo libre");
            }
        }else {
            echo 'test user';
            Message::sendJSONMessage(true, "Erreur serveur");
        }
    }
}