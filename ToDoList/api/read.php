<?php

error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include('../dal/function.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {

    if (isset($_GET['id'])) {
        $toDo = getToDoById($_GET);
        echo $toDo;
    } else {
        $toDoList = getToDoList();
        echo $toDoList;
    }
} else {
    $data = [
        'Status' => 405,
        'Message' => $requestMethod . 'Method is not allowed',
    ];

    header("HTTP/1.0 405 Method is not allowed");
    echo json_encode($data);
}
?>
