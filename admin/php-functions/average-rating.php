<?php
session_start();
require_once '../../function/dbconnect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// The calculation formula within the SQL query is:
// Average( (Words Learned by User / Total Words Available) * 100 )
// This calculates the progress percentage for each user first, then averages those percentages.
$sqlAverageProgress = "
    SELECT 
        AVG((user_progress / total_words) * 100) AS average_progress
    FROM (
        SELECT user_id, COUNT(*) AS user_progress
        FROM word_progress
        GROUP BY user_id
    ) wp
    CROSS JOIN (
        SELECT COUNT(*) AS total_words
        FROM wordcard
    ) tc;
";

$result = $conn->query($sqlAverageProgress);

if ($result && $row = $result->fetch_assoc()) {
    // Check if average_progress is null and default to 0 if it is
    $averageProgress = $row['average_progress'] ?? 0; 
    echo json_encode(['averageProgress' => round((float)$averageProgress, 2)]);
} else {
    echo json_encode(['averageProgress' => 0]);
}

$conn->close();
?>