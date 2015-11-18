<?php

$parse_url = array();

$arg_count = 0;

foreach($_REQUEST as $key => $value){
    $buffer = explode("=", $value);
    $parse_url[$arg_count][0] = $key;
    $parse_url[$arg_count][1] = $value;
    $arg_count++;
}
echo 'Test ma gueule';

$json = file_get_contents('php://input');
echo $json;
echo 'Yolo';
$obj = json_decode($json);
var_dump($obj);

switch(count($parse_url)){
}

?>
