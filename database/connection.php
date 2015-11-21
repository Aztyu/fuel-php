<?php

class Connection{
    public static $key = "3c6e0a7bda61452ce38b4ce020f3d7754a23c5f8eec6ae0271d935c8c854958a";

    public static function ConnectToMySQL(){
        try{
            $bdd = new PDO('mysql:host=localhost;dbname=conso_essence;charset=utf8', 'root', '', array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ));
        }
        catch(Exception $e){
            die('Erreur : '.$e->getMessage());
        }
        return $bdd;
    }
}

?>