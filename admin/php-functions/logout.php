<?php
session_start();

function logout() {
    // Explicitly unset specific session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    unset($_SESSION['user_type']);
    unset($_SESSION['login_start_time']);
    
    // Clear the entire session array as backup
    $_SESSION = array();
    
    // Destroy the session cookie if it exists
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header("Location: ../../loginsignup_page.php");
    exit();
}

logout();