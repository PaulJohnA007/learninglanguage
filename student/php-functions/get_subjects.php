<?php
session_start();
require_once('../../function/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$card_id = isset($_GET['card_id']) ? (int) $_GET['card_id'] : 0;

// Fetch subject cards for the selected learning card
$sql = "SELECT * FROM subjectcard WHERE learning_card_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $card_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($subject = $result->fetch_assoc()) {
    // Calculate progress for each subject
    $subject['progress'] = getSubjectProgress($subject['subject_id'], $conn);
    $subjects[] = $subject;
}

echo json_encode($subjects);

function getSubjectProgress($subjectId, $conn)
{
    // Get total words count
    $totalSql = "SELECT COUNT(*) as total FROM wordcard WHERE subject_card_id = ?";
    $totalStmt = $conn->prepare($totalSql);
    $totalStmt->bind_param("i", $subjectId);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $totalWords = $totalRow['total'];

    if ($totalWords == 0)
        return 0;

    // Get completed words count
    $completedSql = "SELECT COUNT(*) as completed FROM word_progress 
                WHERE word_id IN (SELECT word_id FROM wordcard WHERE subject_card_id = ?) 
                AND user_id = ? AND completed = 1";
    $completedStmt = $conn->prepare($completedSql);
    $completedStmt->bind_param("ii", $subjectId, $_SESSION['user_id']);
    $completedStmt->execute();
    $completedResult = $completedStmt->get_result();
    $completedRow = $completedResult->fetch_assoc();
    $completedWords = $completedRow['completed'];

    // Calculate percentage
    $progress = ($completedWords / $totalWords) * 100;
    return $progress;
}