<?php
session_start();
require_once('../../function/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$subject_id = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;

// Fetch words that haven't been completed yet
$sql = "SELECT w.word_id, w.word, w.phonetic_spelling, w.definition, w.example_sentence, w.word_image 
        FROM wordcard w 
        LEFT JOIN word_progress wp ON w.word_id = wp.word_id 
            AND wp.user_id = ? 
            AND wp.completed = 1
        WHERE w.subject_card_id = ? 
        AND wp.word_id IS NULL
        ORDER BY RAND()";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_SESSION['user_id'], $subject_id);
$stmt->execute();
$result = $stmt->get_result();

$words = [];
while ($word = $result->fetch_assoc()) {
    // Ensure the word_image path is properly formatted
    if (!empty($word['word_image'])) {
        // Map the relative path to the correct directory
        $word['word_image'] = 'admin/word-img/' . basename($word['word_image']);
    }
    $words[] = $word;
}

echo json_encode($words);