<?php
session_start();
require_once('../../function/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch learning cards from database
$sql = "SELECT * FROM learningcard";
$result = $conn->query($sql);

$totalWords = 0;
$completedWords = 0;

while ($card = $result->fetch_assoc()) {
    // Get total words from wordcard table for this card
    $totalWordsSql = "SELECT COUNT(DISTINCT word_id) as total 
                      FROM wordcard w
                      INNER JOIN subjectcard s ON w.subject_card_id = s.subject_id
                      WHERE s.learning_card_id = ?";
    $totalWordsStmt = $conn->prepare($totalWordsSql);
    $totalWordsStmt->bind_param("i", $card['card_id']);
    $totalWordsStmt->execute();
    $totalWordsResult = $totalWordsStmt->get_result()->fetch_assoc();
    $totalWords += $totalWordsResult['total'];

    // Get completed words from word_progress
    $completedWordsSql = "SELECT COUNT(DISTINCT wp.word_id) as completed 
                         FROM word_progress wp
                         INNER JOIN wordcard w ON wp.word_id = w.word_id
                         INNER JOIN subjectcard s ON w.subject_card_id = s.subject_id
                         WHERE wp.user_id = ? 
                         AND s.learning_card_id = ?
                         AND wp.completed = 1";
    $completedStmt = $conn->prepare($completedWordsSql);
    $completedStmt->bind_param("ii", $userId, $card['card_id']);
    $completedStmt->execute();
    $completedWordsResult = $completedStmt->get_result()->fetch_assoc();
    $completedWords += $completedWordsResult['completed'];
}

// Calculate overall progress percentage
$progress = $totalWords > 0 ? round(($completedWords / $totalWords) * 100) : 0;

echo json_encode(['progress' => $progress]);
?>