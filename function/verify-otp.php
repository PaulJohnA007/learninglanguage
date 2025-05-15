<?php
session_start();
require_once 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];

    // Check if the OTP exists in the users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE otp_code = ?");
    $stmt->bind_param("i", $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Store the OTP in the session
        $_SESSION['otp'] = $otp;
        echo json_encode(['success' => true, 'message' => 'OTP verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>