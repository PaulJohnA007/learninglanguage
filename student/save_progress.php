<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure the session is started and user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

// Include the database connection file
include '../config.php';

// Read the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if the necessary data is received
if (isset($data['subject']) && isset($data['progress'])) {
    $user_id = $_SESSION['user_id']; // Assuming the user is logged in
    $subject = mysqli_real_escape_string($conn, $data['subject']);
    $progress = intval($data['progress']);

    // Insert or update the quiz progress for the subject
    $query = "INSERT INTO quiz_progress (user_id, subject, progress) 
              VALUES ('$user_id', '$subject', '$progress') 
              ON DUPLICATE KEY UPDATE progress='$progress'";
    mysqli_query($conn, $query);

    // Subjects for which the progress needs to be calculated
    $subjects = ['Mathematics', 'English', 'Science', 'Music']; // Modify as needed
    $totalProgress = 0;
    
    // Loop through the subjects to calculate the total progress
    foreach ($subjects as $subject) {
        $result = mysqli_query($conn, "SELECT progress FROM quiz_progress WHERE user_id='$user_id' AND subject='$subject'");
        if ($row = mysqli_fetch_assoc($result)) {
            $totalProgress += $row['progress']; // Sum the progress without averaging
        }
    }

    // Update the overall progress in the users table with the total progress
    $updateQuery = "UPDATE users SET overall_progress='$totalProgress' WHERE id='$user_id'";
    mysqli_query($conn, $updateQuery);

    // Return a success response
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
