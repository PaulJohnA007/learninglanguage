<?php
session_start();
require_once '../../function/dbconnect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Fetch total number of subject cards
$sql = "SELECT COUNT(*) as total_subject_cards FROM subjectcard";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['total_subject_cards' => $row['total_subject_cards']]);
} else {
    echo json_encode(['error' => 'Database query failed']);
}
?>