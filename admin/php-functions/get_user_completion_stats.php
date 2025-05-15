<?php
session_start();
require_once '../../function/dbconnect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get time period filter
$timePeriod = isset($_GET['timePeriod']) ? $_GET['timePeriod'] : 'all';
$dateFilter = '';
$subqueryDateFilter = ''; // NEW: separate filter for subqueries without table alias

// Check if the completed_at column exists
try {
    $columnCheckQuery = "SHOW COLUMNS FROM word_progress LIKE 'completed_at'";
    $columnResult = $conn->query($columnCheckQuery);
    $columnExists = $columnResult && $columnResult->num_rows > 0;
    
    if ($columnExists && $timePeriod != 'all') {
        switch($timePeriod) {
            case 'day':
                $dateFilter = " AND DATE(wp.completed_at) = CURDATE()";
                $subqueryDateFilter = " AND DATE(completed_at) = CURDATE()"; // No wp. prefix
                break;
            case 'week':
                $dateFilter = " AND YEARWEEK(wp.completed_at, 1) = YEARWEEK(CURDATE(), 1)";
                $subqueryDateFilter = " AND YEARWEEK(completed_at, 1) = YEARWEEK(CURDATE(), 1)"; // No wp. prefix
                break;
            case 'month':
                $dateFilter = " AND MONTH(wp.completed_at) = MONTH(CURDATE()) AND YEAR(wp.completed_at) = YEAR(CURDATE())";
                $subqueryDateFilter = " AND MONTH(completed_at) = MONTH(CURDATE()) AND YEAR(completed_at) = YEAR(CURDATE())"; // No wp. prefix
                break;
        }
    }
} catch (Exception $e) {
    // If any error occurs checking the column, don't use date filtering
    $dateFilter = '';
    $subqueryDateFilter = '';
}

// Get total number of words
$totalWordsQuery = "SELECT COUNT(*) as total FROM wordcard";
$totalWordsResult = $conn->query($totalWordsQuery);
$totalWords = $totalWordsResult->fetch_assoc()['total'];

// Main query uses wp alias correctly
$query = "
    SELECT 
        u.username,
        IFNULL(COUNT(wp.word_id), 0) AS completed_words,
        ($totalWords - IFNULL(COUNT(wp.word_id), 0)) AS remaining_words,
        ROUND((IFNULL(COUNT(wp.word_id), 0) / $totalWords) * 100, 1) AS completion_percentage
    FROM 
        users u
    LEFT JOIN 
        word_progress wp ON u.id = wp.user_id
    WHERE 
        u.user_type = 'student'" . $dateFilter . "
    GROUP BY 
        u.id, u.username
    ORDER BY 
        completed_words DESC
";

$result = $conn->query($query);
$userData = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $userData[] = $row;
    }
}

// Fixed subquery with correct filter (no wp. alias)
$completedUsersQuery = "
    SELECT COUNT(DISTINCT user_id) as count
    FROM (
        SELECT user_id, COUNT(word_id) as word_count
        FROM word_progress
        WHERE 1=1" . $subqueryDateFilter . "
        GROUP BY user_id
        HAVING word_count = $totalWords
    ) as completed_users
";

$completedResult = $conn->query($completedUsersQuery);
$completedUsers = $completedResult ? $completedResult->fetch_assoc()['count'] : 0;

// Fixed subquery with correct filter (no wp. alias)
$inProgressQuery = "
    SELECT COUNT(DISTINCT user_id) as count
    FROM (
        SELECT user_id, COUNT(word_id) as word_count
        FROM word_progress
        WHERE 1=1" . $subqueryDateFilter . "
        GROUP BY user_id
        HAVING word_count > 0 AND word_count < $totalWords
    ) as in_progress_users
";

$inProgressResult = $conn->query($inProgressQuery);
$inProgressUsers = $inProgressResult ? $inProgressResult->fetch_assoc()['count'] : 0;

// Count users with no progress
$totalUsersQuery = "SELECT COUNT(*) as count FROM users WHERE user_type = 'student'";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['count'];

$notStartedUsers = $totalUsers - ($completedUsers + $inProgressUsers);

// Generate title based on time period
$timePeriodTitle = "All Time";
switch($timePeriod) {
    case 'day': $timePeriodTitle = "Today"; break;
    case 'week': $timePeriodTitle = "This Week"; break;
    case 'month': $timePeriodTitle = "This Month"; break;
}

echo json_encode([
    'userData' => $userData,
    'completedUsers' => $completedUsers,
    'inProgressUsers' => $inProgressUsers,
    'notStartedUsers' => $notStartedUsers,
    'totalUsers' => $totalUsers,
    'totalWords' => $totalWords,
    'timePeriod' => $timePeriod,
    'timePeriodTitle' => $timePeriodTitle
]);

$conn->close();
?>