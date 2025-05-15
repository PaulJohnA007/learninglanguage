<?php
session_start();
require_once('../../function/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$subject_id = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;

// Fetch completed words for the specific subject
$sql = "SELECT w.word, w.word_id, w.phonetic_spelling FROM wordcard w
        JOIN word_progress wp ON w.word_id = wp.word_id
        WHERE w.subject_card_id = ? AND wp.user_id = ? AND wp.completed = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $subject_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$completedWords = [];
while ($row = $result->fetch_assoc()) {
    $completedWords[] = $row;
}

echo json_encode($completedWords);