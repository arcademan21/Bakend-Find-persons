<?php

// Mostrar errores
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// No mostrar errores
error_reporting(0);

// Define list of allowed origins
define("list_of_allowed_origins", [
    $_SERVER["HTTP_HOST"],
    "http://localhost:3000",
    "http://localhost:8888",
    "http://localhost:8080",
    "Thunder Client (https://www.thunderclient.com)",
]);

// Validate http origin
if (
    in_array($_SERVER["HTTP_ORIGIN"], list_of_allowed_origins) ||
    in_array($_SERVER["HTTP_HOST"], list_of_allowed_origins) ||
    in_array($_SERVER["HTTP_REFERER"], list_of_allowed_origins) ||
    in_array($_SERVER["REMOTE_ADDR"], list_of_allowed_origins) ||
    in_array($_SERVER["REMOTE_HOST"], list_of_allowed_origins) ||
    in_array($_SERVER["HTTP_USER_AGENT"], list_of_allowed_origins)
):
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header(
        "Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"
    );
else:
    header("HTTP/1.1 403 Forbidden");
    header("Content-Type: application/json");
    echo '{
        "status": "error",
        "message": "You are not allowed to access this endpoint"
    }';
    exit();
endif;

// Get petition request
$request = json_decode(file_get_contents("php://input"), true);

if (is_null($request)):
    if (empty($_REQUEST)):
        header("HTTP/1.1 400 Bad Request");
        header("Content-Type: application/json");
        echo '{ "status": "error", "message": "Bad request" }';
        exit();
    else:
        $request = json_decode(json_encode($_REQUEST));
    endif;
endif;

// Store petition
$petition = json_decode(json_encode($request));

// Require petitions scheme json
$petitions_scheme = json_decode(file_get_contents("petitions_scheme.json"));
$petition_object = array_keys(get_object_vars($petition->petition->data))[0];

// Validate petition data properties
$data_properties = get_object_vars($petition->petition->data->$petition_object);
$petitions_scheme_properties = get_object_vars($petitions_scheme->$petition_object);
$is_same = array_keys($data_properties) == array_keys($petitions_scheme_properties);

// Validate data types
if ($is_same) {
    foreach ($data_properties as $key => $value) {
        if (gettype($value) != gettype($petitions_scheme_properties[$key])) {
            $is_same = false;
            break;
        }
    }
}

// Validate if is same
if (!$is_same):
    header("HTTP/1.1 400 Bad Request");
    header("Content-Type: application/json");
    echo '{
        "status": "error",
        "message": "Bad request"
    }';
    exit();
endif;

// Requiring crud file
require "controller.php";