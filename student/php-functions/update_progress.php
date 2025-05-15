<?php
session_start();
header('Content-Type: application/json');
require_once('../../function/dbconnect.php');


// Check user session
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'error' => 'User not logged in']));
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];
$wordId = isset($data['word_id']) ? (int) $data['word_id'] : 0;
$score = isset($data['score']) ? (int) $data['score'] : 0;
$cardId = isset($data['card_id']) ? (int) $data['card_id'] : 0;

// Validate required data
if ($wordId <= 0 || $cardId <= 0) {
    die(json_encode(['success' => false, 'error' => 'Invalid input data']));
}

$completed = ($score >= 70) ? 1 : 0;

// Get the subject_id related to the word being updated
$subjectSql = "SELECT s.subject_id FROM wordcard w 
               JOIN subjectcard s ON w.subject_card_id = s.subject_id 
               WHERE w.word_id = ?";
$subjectStmt = $conn->prepare($subjectSql);

if (!$subjectStmt) {
    die(json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]));
}

$subjectStmt->bind_param("i", $wordId);
$subjectStmt->execute();
$result = $subjectStmt->get_result();
$subjectId = $result->num_rows > 0 ? $result->fetch_assoc()['subject_id'] : null;
$subjectStmt->close();

if (!$subjectId) {
    die(json_encode(['success' => false, 'error' => 'Subject not found for this word']));
}

$sql = "INSERT INTO word_progress (user_id, word_id, card_id, pronunciation_score, completed, completed_at) 
        VALUES (?, ?, ?, ?, ?, NOW()) 
        ON DUPLICATE KEY UPDATE 
        pronunciation_score = ?, 
        completed = ?,
        completed_at = NOW()";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]));
}

$stmt->bind_param("iiiiiii", $userId, $wordId, $cardId, $score, $completed, $score, $completed);
if (!$stmt->execute()) {
    die(json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]));
}
$stmt->close();

// First get total number of words for this card
$totalWordsSql = "SELECT COUNT(*) as total 
FROM wordcard w 
JOIN subjectcard s ON w.subject_card_id = s.subject_id 
WHERE s.learning_card_id = ?";

$totalWordsStmt = $conn->prepare($totalWordsSql);
$totalWordsStmt->bind_param("i", $cardId);
$totalWordsStmt->execute();
$totalWords = $totalWordsStmt->get_result()->fetch_assoc()['total'];
$totalWordsStmt->close();

// Then get completed words
$completedWordsSql = "SELECT COUNT(*) as completed 
    FROM word_progress wp 
    WHERE wp.user_id = ? 
    AND wp.card_id = ? 
    AND wp.completed = 1";

$completedStmt = $conn->prepare($completedWordsSql);
$completedStmt->bind_param("ii", $userId, $cardId);
$completedStmt->execute();
$completedWords = $completedStmt->get_result()->fetch_assoc()['completed'];
$completedStmt->close();

// Calculate progress percentage
$progress = ($totalWords > 0) ? ($completedWords * 100 / $totalWords) : 0;

// Return success response with all needed data
echo json_encode([
    'success' => true,
    'progress' => $progress,
    'subject_id' => $subjectId,
    'score' => $score,
    'completed' => $completed == 1
]);

$conn->close();
?>