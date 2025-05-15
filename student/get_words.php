<?php
session_start();
require_once('../function/dbconnect.php');

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$user_id = $_SESSION['user_id'];

// Get words that the user hasn't completed yet
$sql = "SELECT w.* 
        FROM wordcard w 
        LEFT JOIN word_progress wp ON w.word_id = wp.word_id AND wp.user_id = ? AND wp.completed = 1
        WHERE w.subject_card_id = ? 
        AND wp.word_id IS NULL
        ORDER BY w.word_id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $subject_id);
$stmt->execute();
$result = $stmt->get_result();

$words = [];
while($row = $result->fetch_assoc()) {
    $words[] = $row;
}

// Log how many words were found for debugging
error_log("Found " . count($words) . " uncompleted words for subject ID: " . $subject_id . ", user ID: " . $user_id);

header('Content-Type: application/json');
echo json_encode($words);