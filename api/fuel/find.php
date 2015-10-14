<?php

require_once('../../entities/Coordinates.php');
require_once('../../request/Message.php');
require_once('../../entities/FuelStation.php');
require_once('../../entities/FuelPrice.php');
require_once('../../database/connection.php');
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 20/09/2015
 * Time: 10:41
 */

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
        $bdd = ConnectToMySQL();
        if($parse_url[0][0] == 'lat' && $parse_url[1][0] == 'lon' && $parse_url[2][0] == 'radius') {
            $coord = new Coordinates(floatval($parse_url[0][1]), floatval($parse_url[1][1]));
            lookForStation($bdd, $coord, $parse_url[2][1]);
        }
        break;
    default:
        Message::sendJSONMessage(true, "Erreur serveur");
        break;
}

function lookForStation($bdd, $coord, $radius){
    if($radius > 40){
        $radius = 40;
    }
    $s_lat = $coord->getLatitude() - 0.09*($radius/10);      //Check for
    $s_lon = $coord->getLongitude() - 0.125*($radius/10);
    $e_lat = $coord->getLatitude() + 0.09*($radius/10);
    $e_lon = $coord->getLongitude() + 0.125*($radius/10);

    $date = '2014-00-00';       //Base date

    $request = $bdd->prepare("SELECT *
        FROM fuel_station
        WHERE latitude >= :s_lat AND latitude <= :e_lat AND longitude >= :s_lon AND longitude <= :e_lon AND last_update >= :date");    //Only select station with a valid price
    $request->execute(array(
        's_lat' => $s_lat,
        'e_lat' => $e_lat,
        's_lon' => $s_lon,
        'e_lon' => $e_lon,
        'date' => $date
    ));

    $station = array();
    while($donnees = $request->fetch()) {
        $station_row = new FuelStation();
        $station_row->setStationId($donnees['station_id']);
        $station_row->setCoordinates(new Coordinates($donnees['latitude'], $donnees['longitude']));
        $station_row->setPostalCode($donnees['postal_code']);
        $station_row->setAddress($donnees['address']);
        $station_row->setCity($donnees['city']);
        $station_row->setStationName($donnees['station_name']);
        $station_row->setBrand($donnees['brand']);
        $station_row->setLastUpdate($donnees['last_update']);
        $station_row->updateDistance($coord);
        array_push($station, $station_row);
    }

    if(count($station) <= 0){
        Message::sendJSONMessage(true, "Aucune station trouvée");
        return;
    }

    for($i = 0; $i < count($station); $i++) {
        if ($station[$i]->getLastUpdate() > 0) {
            $station_request = $bdd->prepare("SELECT *
                FROM fuel_price
                WHERE station_id = :station_id");
            $station_request->execute(array(
                'station_id' => $station[$i]->getLastUpdate()
            ));

            while ($donnees = $station_request->fetch()) {
                $station[$i]->setFuelPrice(new FuelPrice($donnees['diesel_price'], $donnees['petrol95_price'], $donnees['petrol95E10_price'], $donnees['petrol98_price'], $donnees['gpl_price']));
            }
        }
    }
    Message::sendJSONMessage(false, $station);
}