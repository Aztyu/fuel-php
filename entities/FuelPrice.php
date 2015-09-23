<?php
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 21/09/2015
 * Time: 23:02
 */

class FuelPrice implements JsonSerializable{
    private $diesel;
    private $sp95;
    private $sp95e10;
    private $sp98;
    private $gpl;

    function __construct($diesel, $sp95, $sp95e10, $sp98, $gpl){
        $this->diesel = $diesel;
        $this->sp95 = $sp95;
        $this->sp95e10 = $sp95e10;
        $this->sp98 = $sp98;
        $this->gpl = $gpl;
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
            'diesel' => $this->diesel,
            'sp95' => $this->sp95,
            'sp95e10' => $this->sp95e10,
            'sp98' => $this->sp98,
            'gpl' => $this->gpl,
        ];
    }
}