<?php

require_once('../../entities/FuelPrice.php');
require_once('../../database/connection.php');
require_once('../../utils/Logger.php');

if(isset($_GET["passkey"])){
    $json = file_get_contents('php://input');
    $obj = json_decode($json, true);

    $data = date("d/m/Y h:i:s a", time())." - number was : ".count($obj);
    Logger::debugLog($data);

    foreach($obj as $price){
        $station_id = $price['id'];
        $fuel = new FuelPrice($price['gazole'], $price['sp95'], $price['sp95e10'], $price['sp98'], $price['gpl']);
        $fuel->insertToDB($station_id);
    }
}else{
    echo "Please pass a key";
}
?>