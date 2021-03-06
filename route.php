<?php
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 26/12/2015
 * Time: 15:24
 */

require_once 'vendor/autoload.php';
require_once 'controller/api/ApiUserController.php';
require_once 'controller/api/ApiFuelController.php';
require_once 'controller/api/SiteController.php';
require_once 'entities/Coordinates.php';

use Pux\Mux;
use Pux\Executor;

$path = explode("?" , $_SERVER['REQUEST_URI'])[0];

$api_mux = new Mux;
$api_user_mux = new Mux;
$api_fuel_mux = new Mux;
$main_mux = new Mux;

$api_user_mux->get('/check', ['ApiUserController','checkAction']);
$api_user_mux->get('/connect', ['ApiUserController','connectAction']);
$api_user_mux->get('/create', ['ApiUserController','createAction']);
$api_mux->mount('/user', $api_user_mux);

$api_fuel_mux->get('/find', ['ApiFuelController','findAction']);
$api_fuel_mux->post('/insert', ['ApiFuelController','insertAction']);
$api_mux->mount('/fuel', $api_fuel_mux);

$main_mux->mount('/api', $api_mux);

$main_mux->get('/', ['SiteController','indexAction']);

$route = $main_mux->dispatch($path);
if($route == null){
    $route = $main_mux->dispatch('/');
}
echo Executor::execute($route);

?>