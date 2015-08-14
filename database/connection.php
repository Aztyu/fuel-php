<?php
function ConnectToMySQL(){
    try
    {
        $bdd = new PDO('mysql:host=localhost;dbname=conso_essence;charset=utf8', 'root', '');
    }
    catch(Exception $e)
    {
        die('Erreur : '.$e->getMessage());
    }
    return $bdd;
}
?>