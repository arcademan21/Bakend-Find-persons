<?php

// Todo: Validate if server is same as host
// if ( $_SERVER["HTTP_ORIGIN"] !== $_SERVER["HTTP_HOST"] ):
//     header("HTTP/1.1 403 Forbidden");
//     header("Content-Type: application/json");
//     echo '{
//         "status": "error",
//         "message": "You are not allowed to access this endpoint"
//     }';
//     exit();
// endif;

// Database configuration (produccion)
$db_name = "bkphkluq_find-persons-new";
$db_user = "bkphkluq_ndooburm";
$db_pass = "pROgY547Ge221";
$smtp_password = "pROgY547Ge221";

// Database configuration (desarrollo)
// $db_name = "find-persons-new";
// $db_user = "root";
// $db_pass = "root";

// Firebase configuration
$apiKey = "AIzaSyCyk37Ey6buyE_a7zLwrLggvcg0Z-VAVhE";
$authDomain = "find-persons-8a0c0.firebaseapp.com";
$projectId = "find-persons-8a0c0";
$storageBucket = "find-persons-8a0c0.appspot.com";
$messagingSenderId = "470088672223";
$appId = "1:470088672223:web:c0d09a8fbf061c5912da6a";
$measurementId = "G-8ZPKF59SD8";

// Payments configuration
// Aqui paycomet
