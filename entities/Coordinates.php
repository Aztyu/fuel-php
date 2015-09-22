<?php
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 20/09/2015
 * Time: 18:33
 */

class Coordinates{
    private $latitude;
    private $longitude;

    function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getLatitude(){
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude){
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude(){
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude){
        $this->longitude = $longitude;
    }

    private function toRadius($number){
        return ($number * pi()) / 180;
    }

    public function getDistance($coord){
        $radius = 6371; // earth's mean radius in km
        $diff_lat = $this->toRadius($this->latitude - $coord->latitude);
        $diff_lon = $this->toRadius($this->longitude - $coord->longitude);
        $lat1 = $this->toRadius($this->latitude);
        $lat2 = $this->toRadius($coord->getLatitude());
        $a = (sin($diff_lat/2) * sin($diff_lat/2)) + cos($lat1) * cos($lat2) * sin($diff_lon/2) * sin($diff_lon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $d = $radius * $c;
        return $d;
    }


}