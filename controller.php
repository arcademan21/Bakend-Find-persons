<?php

// ---------------------------------------------------------
/*
* Controller petitions.
* This controller will call handle all petitions from the client.
*/
// ---------------------------------------------------------

// Validate request
try {

    // Require model
    require './model.php';
    
    // Store petition name
    $petition_name = $petition->petition->name;

    // Creating new user
    if( $petition_name === 'create_new_user' ) :
        echo $Crud->create_new_user( $petition );
        exit;

    // Getting user
    elseif( $petition_name === 'get_user' ) :
        echo $Crud->get_user( $petition );
        exit;

    // Getting users
    elseif( $petition_name === 'get_users' ) :
        echo $Crud->get_users( $petition );
        exit;

    // Update user
    elseif( $petition_name === 'update_user' ) :
        echo $Crud->update_user_password( $petition );
        exit;

    // Delete user
    elseif( $petition_name === 'delete_user' ) :
        echo $Crud->delete_user( $petition );
        exit;

    // Creating new email
    elseif( $petition_name === 'send_email' ) :
        echo $Crud->send_email( $petition );
        exit;

    // Getting searchs
    elseif( $petition_name === 'get_searchs' ) :
        echo $Crud->get_searchs( $petition );
        exit;

    // Update suscription
    elseif( $petition_name === 'update_suscription' ) :
        echo $Crud->update_suscription( $petition );
        exit;

    // Update searchs
    elseif( $petition_name === 'update_searchs' ) :
        echo $Crud->update_searchs( $petition );
        exit;

    // Getting downloads
    elseif( $petition_name === 'get_downloads' ) :
        echo $Crud->get_downloads( $petition );
        exit;

    // Update downloads
    elseif( $petition_name === 'update_downloads' ) :
        echo $Crud->update_downloads( $petition );
        exit;

    // Return error
    else :
        header('HTTP/1.1 400 Bad Request');
        header('Content-Type: application/json');
        echo '{
            "status": "error",
            "message": "Bad request"
        }'; exit;
    endif;


} catch ( Exception $e ) {
    
    // Return error
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    echo '{
        "status": "error",
        "message": "Internal Server Error",
        "error": "'.$e->getMessage().'"
    }'; exit;
    
}