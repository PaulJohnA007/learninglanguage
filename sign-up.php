<?php
session_start();

require_once 'function/dbconnect.php';

// Check if POST data is set
if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    // Get user input
    $inputUsername = $_POST['username'];
    $inputEmail = $_POST['email'];
    $inputPassword = $_POST['password'];
    $inputGradeLevel = $_POST['grade_level'];
    
    
    // Handle file upload
    $profileImage = NULL;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['profile_image']['name']);
        
        // Ensure the uploads directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
            $profileImage = $uploadFile;
        } else {
            header("Location: loginsignup_page.php?error=upload_error");
            exit();
        }
    }

    // First check if the username already exists
    $checkUser = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $checkUser->bind_param("s", $inputUsername);
    $checkUser->execute();
    $result = $checkUser->get_result();
    $checkUser->close();
    
    if ($result->num_rows > 0) {
        // Username already exists
        header("Location: loginsignup_page.php?error=username_exists");
        exit();
    }
    
    // Then check if the email already exists
    $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $inputEmail);
    $checkEmail->execute();
    $result = $checkEmail->get_result();
    $checkEmail->close();
    
    if ($result->num_rows > 0) {
        // Email already exists
        header("Location: loginsignup_page.php?error=email_exists");
        exit();
    }

    // Now try to insert the new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_image, grade_level, user_type) VALUES (?, ?, ?, ?, ?, 'student')");
    $stmt->bind_param("sssss", $inputUsername, $inputEmail, $inputPassword, $profileImage, $inputGradeLevel);
    $success = $stmt->execute();
    $stmt->close();


    if ($success) {
        // Success - redirect to login page
        header("Location: loginsignup_page.php?success=1");
        exit();
    } else {
        // Any other error - redirect with general error
        header("Location: loginsignup_page.php?error=database_error");
        exit();
    }
}

$conn->close();
?>