<?php

require_once('../../entities/FuelPrice.php');
require_once('../../database/connection.php');

$json = file_get_contents('php://input');
$obj = json_decode($json, true);


foreach($obj as $price){
    $station_id = $price['id'];
    $fuel = new FuelPrice($price['gazole'], $price['sp95'], $price['sp95e10'], $price['sp98'], $price['gpl']);
    var_dump($fuel);
    $fuel->insertToDB($station_id);
}
?>