<?php

error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: access");

include('utility.php');


$requestMethod = $_SERVER['REQUEST_METHOD'];

if($requestMethod == "POST")
{
    $inputData = json_decode(file_get_contents("php://input"),true);
    if(empty($inputData))
    {
        $userData = resetPassword($_POST);
    }else{
        $userData = resetPassword($inputData);
    }
    echo $userData;
}else{
    $data = [
        'status' => 405,
        'message' => $requestMethod , " Method Not Allowed",
    ];

    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
