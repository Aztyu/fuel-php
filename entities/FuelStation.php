<?php

//require_once('../entities/FuelPrice.php');
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 20/09/2015
 * Time: 10:41
 */

class FuelStation implements JsonSerializable{
    private $station_id;
    private $coordinates;
    private $postal_code;
    private $address;
    private $city;
    private $station_name;
    private $brand;
    private $last_update;
    private $distance;
    private $fuel_price;

    function __construct(){
        $this->station_id = 0;
        $this->coordinates = new Coordinates(0.0, 0.0);
        $this->postal_code = 0;
        $this->address = "";
        $this->city = "";
        $this->station_name = "";
        $this->brand = "";
        $this->last_update = 0;
        $this->distance = 0.0;
        $this->fuel_price = new FuelPrice(0, 0, 0, 0, 0);
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize(){
        return [
            'station_id' => $this->station_id,
            'coordinates' => $this->coordinates,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'city' => $this->city,
            'station_name' => $this->station_name,
            'brand' => $this->brand,
            'last_update' => $this->last_update,
            'distance' => $this->distance,
            'fuel_price' => $this->fuel_price
        ];
    }

    /**
     * @param string $last_update
     */
    public function setLastUpdate($last_update){
        $this->last_update = $last_update;
    }

    /**
     * @param string $brand
     */
    public function setBrand($brand){
        $this->brand = $brand;
    }

    /**
     * @param string $station_name
     */
    public function setStationName($station_name){
        $this->station_name = $station_name;
    }

    /**
     * @param string $city
     */
    public function setCity($city){
        $this->city = $city;
    }

    /**
     * @param string $address
     */
    public function setAddress($address){
        $this->address = $address;
    }

    /**
     * @param int $postal_code
     */
    public function setPostalCode($postal_code){
        $this->postal_code = $postal_code;
    }

    /**
     * @param Coordinates $coordinates
     */
    public function setCoordinates($coordinates){
        $this->coordinates = $coordinates;
    }

    /**
     * @param int $station_id
     */
    public function setStationId($station_id){
        $this->station_id = $station_id;
    }

    /**
     * @param mixed $distance
     */
    public function setDistance($distance){
        $this->distance = $distance;
    }

    public function setFuelprice($price){
        $this->fuel_price = $price;
    }

    /**
     * @return string
     */
    public function getLastUpdate(){
        return $this->last_update;
    }

    /**
     * @return int
     */
    public function getStationId(){
        return $this->station_id;
    }

    public function updateDistance($start_coord){
        $this->distance = $this->coordinates->getDistance($start_coord);
    }

    public function asJson(){
        return $this;
    }
}