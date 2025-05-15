<?php
session_start();
require_once 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    // Update the user's password in the database and set otp_code to NULL
    $stmt = $conn->prepare("UPDATE users SET password = ?, otp_code = NULL WHERE otp_code = ?");
    $stmt->bind_param("si", $password, $_SESSION['otp']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>