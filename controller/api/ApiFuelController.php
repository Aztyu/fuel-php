<?php

/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 26/12/2015
 * Time: 21:02
 */
require_once 'database/connection.php';
require_once 'request/Message.php';
require_once 'entities/User.php';
require_once 'entities/FuelStation.php';
require_once 'entities/FuelPrice.php';
require_once 'utils/Logger.php';

class ApiFuelController extends \Pux\Controller{
    public function findAction(){
        $bdd = Connection::ConnectToMySQL();
        if(isset($_GET['lat']) && isset($_GET['lon'])) {
            if(isset($_GET['radius'])){
                $radius = $_GET['radius'];
            }else {
                $radius = 10;
            }
            $coord = new Coordinates(floatval($_GET['lat']), floatval($_GET['lon']));

            if($radius > 40){
                $radius = 40;
            }
            $s_lat = $coord->getLatitude() - 0.09*($radius/10);      //Check for
            $s_lon = $coord->getLongitude() - 0.125*($radius/10);
            $e_lat = $coord->getLatitude() + 0.09*($radius/10);
            $e_lon = $coord->getLongitude() + 0.125*($radius/10);

            $date = '2014-00-00';       //Base date

            $request = $bdd->prepare("SELECT * FROM fuel_station WHERE latitude >= :s_lat AND latitude <= :e_lat AND longitude >= :s_lon AND longitude <= :e_lon AND last_update >= :date");    //Only select station with a valid price
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
                    $station_request = $bdd->prepare("SELECT * FROM fuel_price WHERE price_id = :price_id");
                    $station_request->execute(array(
                        'price_id' => $station[$i]->getLastUpdate()
                    ));

                    while ($donnees = $station_request->fetch()) {
                        $station[$i]->setFuelPrice(new FuelPrice($donnees['diesel_price'], $donnees['petrol95_price'], $donnees['petrol95E10_price'], $donnees['petrol98_price'], $donnees['gpl_price']));
                    }
                }
            }
            Message::sendJSONMessage(false, $station);
        }
    }

    public function insertAction(){
        if(isset($_GET["passkey"]) && $_GET["passkey"] == Connection::$key){
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
            echo "Please pass a valid key";
        }
    }

}