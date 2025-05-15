<?php
session_start();
header('Content-Type: application/json');

include '../../function/dbconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}


// Query to group users based on login time ranges
$sqlLoginDistribution = "
    SELECT
        CASE
            WHEN total_login_time < 3600 THEN 'Less than 1 hour'
            WHEN total_login_time BETWEEN 3600 AND 18000 THEN '1–5 hours'
            ELSE 'More than 5 hours'
        END AS login_range,
        COUNT(*) AS user_count
    FROM users
    GROUP BY login_range
    ORDER BY FIELD(login_range, 'Less than 1 hour', '1–5 hours', 'More than 5 hours');
";

$result = $conn->query($sqlLoginDistribution);

$loginTimeData = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $loginTimeData[] = [
            'range' => $row['login_range'],
            'count' => $row['user_count']
        ];
    }
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($loginTimeData);

$conn->close();
?>
