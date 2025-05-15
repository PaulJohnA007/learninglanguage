<?php
session_start();
require_once '../../function/dbconnect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Fetch total number of words
$sql = "SELECT COUNT(*) as total_words FROM wordcard";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['total_words' => $row['total_words']]);
} else {
    echo json_encode(['error' => 'Database query failed']);
}
?>