<?php
session_start();

// Database connection (Update with your actual database credentials)
// $servername = "sql312.infinityfree.com";
// $username = "if0_38512289";
// $password = "learning143";
// $dbname = "if0_38512289_englishlearn";

require_once('../function/dbconnect.php');

// Ensure the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Calculate the session duration
    if (isset($_SESSION['login_start_time'])) {
        $loginStartTime = $_SESSION['login_start_time'];
        $loginDuration = time() - $loginStartTime; // Duration in seconds

        // Update total login time in the database
        $updateSql = "UPDATE users SET total_login_time = total_login_time + ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param('ii', $loginDuration, $userId);
        $updateStmt->execute();
        $updateStmt->close();
    }

    // Clear session data
    session_unset();
    session_destroy();
}

// Close the database connection
$conn->close();

// Redirect to the login page
header("Location: ../loginsignup_page.php");
exit();
?>
