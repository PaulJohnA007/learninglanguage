<?php
session_start();
require_once('../../function/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

try {
    // Count completed words from word_progress and total words from wordcard
    $sql = "SELECT 
            (SELECT COUNT(DISTINCT word_id) 
             FROM word_progress 
             WHERE user_id = ? AND completed = 1) as completed_words,
            (SELECT COUNT(word_id) 
             FROM wordcard) as total_words";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $progress = [
        'total_words' => $row['total_words'],
        'completed_words' => $row['completed_words'],
        'progress' => $row['total_words'] > 0 
            ? round(($row['completed_words'] / $row['total_words']) * 100) 
            : 0
    ];
    
    echo json_encode([
        'success' => true, 
        'progress' => $progress
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}