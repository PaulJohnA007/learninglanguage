<?php
session_start();
header('Content-Type: application/json');

include '../../function/dbconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}


// Query to calculate progress for each subject
$sqlSubjectProgress = "
    SELECT
        sc.subject_type, -- Replace subject_id with subject_type for a more descriptive name
        COALESCE(SUM(CASE WHEN wp.word_id IS NOT NULL THEN 1 ELSE 0 END), 0) AS words_completed,
        COUNT(wc.word_id) AS total_words,
        ROUND(COALESCE(SUM(CASE WHEN wp.word_id IS NOT NULL THEN 1 ELSE 0 END), 0) / COUNT(wc.word_id) * 100, 2) AS progress_percentage
    FROM subjectcard sc
    LEFT JOIN wordcard wc ON sc.subject_id = wc.subject_card_id
    LEFT JOIN word_progress wp ON wp.word_id = wc.word_id AND wp.user_id = ?
    GROUP BY sc.subject_type
    ORDER BY sc.subject_type;
";

// Replace '1' with your logged-in user's ID
$stmt = $conn->prepare($sqlSubjectProgress);
$stmt->bind_param('i', $userId);
$userId = 1; // Example user ID; replace dynamically
$stmt->execute();
$result = $stmt->get_result();

$progressData = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $progressData[] = [
            'subject' => $row['subject_type'], // Use subject_type instead of subject_id
            'percentage' => $row['progress_percentage']
        ];
    }
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($progressData);

$conn->close();
?>
