<?php

// Hide errors
error_reporting(0);

require 'config.php';

try {
    // Database connection
    $db = new PDO( "mysql:host=localhost;dbname=$db_name", $db_user, $db_pass );
    $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

} catch ( PDOException $e ) {

    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    echo '{
        "status": "error",
        "message": "Database connection error"
        "error": "'.$e->getMessage().'"
    }'; exit;

}