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
            Message::sendJSONMessage(true, "Erreur serveur");
        }
    }

    public function connectAction(){
        $bdd = Connection::ConnectToMySQL();
        if(isset($_GET['password'])){
            if(isset($_GET['pseudo'])){
                $user = User::getUser($bdd, $_GET['password'], $_GET['pseudo'], null);
                if($user){
                    Message::sendJSONMessage(false, $user->asArray());
                }else{
                    Message::sendJSONMessage(true, "Nom d'utilisateur ou mot de passe invalide");
                }
            }else if(isset($_GET['email'])){
                $user = User::getUser($bdd, $_GET['password'], null, $_GET['email']);
                if($user){
                    Message::sendJSONMessage(false, $user->asArray());
                }else{
                    Message::sendJSONMessage(true, "Email ou mot de passe invalide");
                }
            }else{
                Message::sendJSONMessage(true, "Nom d'utilisateur ou email invalide");
            }
        }else{
            Message::sendJSONMessage(true, "Veuillez entrer votre mot de passe");
        }
    }

    public function createAction(){
        $bdd = Connection::ConnectToMySQL();
        if(isset($_GET['pseudo']) && isset($_GET['password']) && isset($_GET['email'])){
            $new_user = new User($_GET['pseudo'], $_GET['password'], $_GET['email']);
            $new_user->addToDatabase();
        }else {
            Message::sendJSONMessage(false, "Requete invalide");
        }
    }
}