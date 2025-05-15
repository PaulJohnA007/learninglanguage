<?php
session_start();
require_once('../../function/dbconnect.php');

// Prevent PHP from displaying errors directly (which breaks JSON)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set proper JSON headers
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['error' => 'Not logged in']);
  exit();
}

// Get subject_id from request
$subject_id = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;

if ($subject_id <= 0) {
  echo json_encode(['error' => 'Invalid subject ID']);
  exit();
}

try {
  // Get total words count
  $totalSql = "SELECT COUNT(*) as total FROM wordcard WHERE subject_card_id = ?";
  $totalStmt = $conn->prepare($totalSql);
  $totalStmt->bind_param("i", $subject_id);
  $totalStmt->execute();
  $totalResult = $totalStmt->get_result();
  $totalRow = $totalResult->fetch_assoc();
  $totalWords = $totalRow['total'];

  if ($totalWords == 0) {
    echo json_encode([
      'subject_id' => $subject_id,
      'progress' => 0,
      'total_words' => 0,
      'completed_words' => 0
    ]);
    exit();
  }

  // Get completed words count
  $completedSql = "SELECT COUNT(*) as completed FROM word_progress 
                 WHERE word_id IN (SELECT word_id FROM wordcard WHERE subject_card_id = ?) 
                 AND user_id = ? AND completed = 1";
  $completedStmt = $conn->prepare($completedSql);
  $completedStmt->bind_param("ii", $subject_id, $_SESSION['user_id']);
  $completedStmt->execute();
  $completedResult = $completedStmt->get_result();
  $completedRow = $completedResult->fetch_assoc();
  $completedWords = $completedRow['completed'];

  // Calculate percentage
  $progress = ($completedWords / $totalWords) * 100;

  echo json_encode([
    'subject_id' => $subject_id,
    'progress' => $progress,
    'total_words' => $totalWords,
    'completed_words' => $completedWords
  ]);
  
} catch (Exception $e) {
  // Log error to a file instead of displaying it
  error_log("Error in get_progress.php: " . $e->getMessage());
  echo json_encode([
    'error' => 'Database error',
    'message' => $e->getMessage()
  ]);
}
?>