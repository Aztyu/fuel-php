<?php

include('connection.php');
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
        if($parse_url[0][0] == 'username' && $parse_url[1][0] == 'password'){
            Connection($parse_url[0][1], $parse_url[1][1]);
        }elseif($parse_url[0][0] == 'new_username' && $parse_url[1][0] == 'password'){
            Inscription($parse_url[0][1], $parse_url[1][1]);
        }
        break;
    case 3:
        if($parse_url[0][0] == 'username' && $parse_url[1][0] == 'password' && $parse_url[2][0] == 'get_flatshare') {
            Flatshare($parse_url[0][1], $parse_url[1][1]);
        }elseif($parse_url[0][0] == 'username' && $parse_url[1][0] == 'password' && $parse_url[2][0] == 'get_request') {
            getRequests($parse_url[0][1], $parse_url[1][1]);
        }else if($parse_url[0][0] == 'username' && $parse_url[1][0] == 'password' && $parse_url[2][0] == 'create_flatshare'){
            createFlatshare($parse_url[0][1], $parse_url[1][1], $parse_url[2][1]);
        }else if($parse_url[0][0] == 'username' && $parse_url[1][0] == 'password' && $parse_url[2][0] == 'find_flatshare'){
            findFlatshare($parse_url[0][1], $parse_url[1][1], $parse_url[2][1]);
        }
        break;
    case 4:
        if($parse_url[0][0] == 'username' && $parse_url[1][0] == 'password' && $parse_url[2][0] == 'flatshare_id' && $parse_url[3][0] == 'creator_id'){
            makeRequestFlatshare($parse_url[0][1], $parse_url[1][1], $parse_url[2][1], $parse_url[3][1]);
        }
        break;
    default:
        echo "Fail";
        break;
}

function Connection($user, $passwd){

    $bdd = ConnectToMySQL();

    $reponse = $bdd->prepare('SELECT Name FROM user WHERE Name = ? AND Password = ?');
    $reponse->execute(array($user, $passwd));

    if($donnees = $reponse->fetch()){
        echo "Ok";
    }else{
        $reponse = $bdd->prepare('SELECT Name FROM user WHERE Name = ?');
        $reponse->execute(array($user));
        if($donnees = $reponse->fetch())
        {
            echo "Bad password";
        }else{
            echo "Bad username";
        }
    }

    $reponse->closeCursor();
}

function Inscription($user, $passwd){

    $bdd = ConnectToMySQL();

    $request = $bdd->prepare('SELECT Name FROM user WHERE Name = ?');
    $request->execute(array($user));

    if($donnees = $request->fetch()){
        echo 'Le nom ' . $donnees['Name'] . ' est deja pris';
    }else{
        try
        {
            $request = $bdd->prepare('INSERT INTO user(Name, Password, Email, Flatshare_id) VALUES(:user, :passwd, DEFAULT, DEFAULT)');
            $request->execute(array(
                'user' => $user,
                'passwd' => $passwd
            ));
        }
        catch(Exception $e)
        {
            die('Erreur : '.$e->getMessage());
        }
        echo "Utilisateur rajoute";
    }
    $request->closeCursor();
}

function Flatshare($user, $passwd){

    $bdd = ConnectToMySQL();

    $request = $bdd->prepare('SELECT Name FROM user WHERE Name = ? AND Password = ?');
    $request->execute(array($user, $passwd));

    if($donnees = $request->fetch()){
        $request = $bdd->prepare('SELECT * FROM flatshare WHERE flatshare_id = (SELECT flatshare_id from user WHERE Name = ? && Password = ?)');
        $request->execute(array($user, $passwd));

        if($donnees = $request->fetch()){
            echo $donnees['flatshare_id'] . ',' . $donnees['Name'];
        }else{
            echo "No flatshare";
        }
    }else{
        echo "Invalid username";
    }
    $request->closeCursor();
}

function createFlatshare($user, $passwd, $name){

    $bdd = ConnectToMySQL();

    $request = $bdd->prepare('SELECT Name, user_id, flatshare_id FROM user WHERE Name = ? AND Password = ?');
    $request->execute(array($user, $passwd));

    if($donnees = $request->fetch()){
        $flattest = $bdd->prepare('SELECT * FROM flatshare WHERE Name = ?');
        $flattest->execute(array($name));

        if($flatshare = $flattest->fetch()){
            echo "Flatshare already exists";
        }else if($donnees['flatshare_id'] != NULL) {
            echo "User already have a flatshare";
        }else{
            $creation = $bdd->prepare('INSERT INTO flatshare(Name, creator_id) VALUES(:name, :creator)');
            $creation->execute(array(
                'name' => $name,
                'creator' => $donnees['user_id']
            ));

            $req = $bdd->prepare('UPDATE user SET flatshare_id = (SELECT flatshare_id FROM flatshare WHERE Name =:flatshare_name) WHERE user_id = :user_id');
            $req->execute(array(
                'flatshare_name' => $name,
                'user_id' => $donnees['user_id']
            ));
            echo "Flatshare created";
        }
        $flattest->closeCursor();
    }else{
        echo "Invalid username";
    }
    $request->closeCursor();
}

function findFlatshare($user, $passwd, $name){

    $bdd = ConnectToMySQL();

    $request = $bdd->prepare('SELECT Name FROM user WHERE Name = ? AND Password = ?');
    $request->execute(array($user, $passwd));

    if($donnees = $request->fetch()){
        $name = '%' . $name .'%';
        //$flattest = $bdd->prepare('SELECT * FROM flatshare WHERE Name = ?');
        $flattest = $bdd->prepare('SELECT * FROM flatshare WHERE Name LIKE ?');
        $flattest->execute(array($name));

        if($flatshare = $flattest->fetch()){
            echo $flatshare['flatshare_id'] . ',' . $flatshare['creator_id'] . ',' . $flatshare['Name'] . '~';
            while($flatshare = $flattest->fetch()){
                echo $flatshare['flatshare_id'] . ',' . $flatshare['creator_id'] . ',' . $flatshare['Name'] . '~';
            }
        }else if($flattest['flatshare_id'] != NULL) {
            echo "No flatshare found with this name";
        }
        $flattest->closeCursor();
    }else{
        echo "Invalid username";
    }
    $request->closeCursor();
}

function makeRequestFlatshare($user, $passwd, $flat_id, $creator_id){
    $bdd = ConnectToMySQL();

    $request = $bdd->prepare('SELECT Name, user_id FROM user WHERE Name = ? AND Password = ?');
    $request->execute(array($user, $passwd));

    if($donnees = $request->fetch()){
        //Send a request with requestor, requested, text, status(=Pending), flatsahre_id

        $request_nbr = $bdd->prepare('SELECT COUNT(*) as "number" FROM request WHERE requestor_id = ?');
        $request_nbr->execute(array($donnees['user_id']));
        if($donnees_nbr = $request_nbr->fetch()) {
            if ($donnees_nbr['number'] >= 5) {
                echo "You cannot send request anymore(Request are limited to 5)";
            } else {
                $creation = $bdd->prepare('INSERT INTO request(requestor_id, requested_id, request_text, flatshare_id) VALUES(:requestor, :requested, :text, :flatshare_id)');
                $creation->execute(array(
                    'requestor' => $donnees['user_id'],
                    'requested' => $creator_id,
                    'text' => "Hi can I come in ?",
                    'flatshare_id' => $flat_id
                ));
                echo "Request sent";
            }
        }
        $request_nbr->closeCursor();
    }else{
        echo "Invalid username";
    }
    $request->closeCursor();
}

function getRequests($user, $passwd){
    $bdd = ConnectToMySQL();

    $request = $bdd->prepare('SELECT Name, user_id FROM user WHERE Name = ? AND Password = ?');
    $request->execute(array($user, $passwd));

    if($donnees = $request->fetch()){
        $request_nbr = $bdd->prepare('SELECT requestor_id, request_text FROM request WHERE requested_id = ?');
        $request_nbr->execute(array($donnees['user_id']));

        if($donnees_nbr = $request_nbr->fetch()) {
            echo $donnees_nbr['requestor_id'] . "," . $donnees_nbr['request_text'] . "~";
            while($donnees_nbr = $request_nbr->fetch()){
                echo $donnees_nbr['requestor_id'] . "," . $donnees_nbr['request_text'] . "~";
            }
        }else{
            echo "You have no request";
        }
        $request_nbr->closeCursor();
    }else{
        echo "Invalid username";
    }
    $request->closeCursor();
}
?>
