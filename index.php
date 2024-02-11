<?php

// var_dump($_SERVER["REQUEST_URI"]); //string(17) "/project/restapi/"

// http://localhost/project/restapi/123
declare (strict_types = 1);

// class를 자동로드
spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

// 에러처리 :
set_error_handler("ErrorHandler::handleError");

// 예외처리
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);
// print_r($parts);
// Array
// (
//     [0] =>
//     [1] => project
//     [2] => restapi
//     [3] => 123
// )

if ($parts[2] != "restapi") {
    http_response_code(404);
    exit;
}

$id = $parts[3] ?? null;

$database = new Database("localhost", "product_db", "root", "11223344");

$gateway = new ProductGateway($database);

$controller = new ProductController($gateway);

$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
