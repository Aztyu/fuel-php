<?php
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 21/09/2015
 * Time: 23:02
 */

class FuelPrice {
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
}