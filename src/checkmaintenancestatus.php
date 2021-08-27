<?php
    /*-------------------------------------------------------------------------
    
        This page is designed to redirect all users (except for administrative
        users) to the maintenance break page until the set time.

        To activate, just uncomment the lines of codes below to redirect the
        users to the maintenance page. 

        Before activating the maintenance page, please ensure that the
        administrative users are logged in to avoid getting locked out of the
        site.
    
    -------------------------------------------------------------------------*/
    /*

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['status']) || $_SESSION['status'] > 0){ //kick non-admin users
        session_destroy();
        header("Location: maintenance.php");
        exit();   
    }

    */
    
?>