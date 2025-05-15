<?php
session_start();
require_once('../../function/dbconnect.php');

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['error' => 'Not logged in']);
  exit();
}

// Get parameters
$word_text = isset($_GET['word']) ? trim($_GET['word']) : '';
$subject_id = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;

if (empty($word_text)) {
  echo json_encode(['error' => 'Word text is required']);
  exit();
}

// First try to find the exact word
$sql = "SELECT word_id FROM wordcard WHERE word = ? ";
$params = [$word_text];
$types = "s";

// If subject_id is provided, add it to the query
if ($subject_id > 0) {
  $sql .= "AND subject_card_id = ?";
  $params[] = $subject_id;
  $types .= "i";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  // Found exact match
  echo json_encode([
    'success' => true,
    'word_id' => $row['word_id'],
    'match_type' => 'exact'
  ]);
  exit();
}

// If no exact match, try case-insensitive search
$sql = "SELECT word_id FROM wordcard WHERE LOWER(word) = LOWER(?)";
$params = [$word_text];
$types = "s";

if ($subject_id > 0) {
  $sql .= " AND subject_card_id = ?";
  $params[] = $subject_id;
  $types .= "i";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  // Found case-insensitive match
  echo json_encode([
    'success' => true,
    'word_id' => $row['word_id'],
    'match_type' => 'case_insensitive'
  ]);
  exit();
}

// No match found
echo json_encode([
  'success' => false,
  'error' => 'Word not found'
]);
?>