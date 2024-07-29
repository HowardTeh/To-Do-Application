<?php

error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include('../dal/function.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "PUT") {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (empty($inputData)) {
        $updateToDo = updateToDo($_POST, $_GET);
    } else {
        $updateToDo = updateToDo($inputData, $_GET);
    }

    echo $updateToDo;
} else {
    $data = [
        'Status' => 405,
        'Message' => $requestMethod . ' Method is not allowed',
    ];

    header("HTTP/1.0 405 Method is not allowed");
    echo json_encode($data);
}
?>

