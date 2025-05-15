<?php
// Start the session
session_start();

require_once('../function/dbconnect.php');


// Retrieve the logged-in user's ID from the session
$userId = $_SESSION['user_id'] ?? 0; // Default to 0 if not set

// Check if the user ID is valid
if ($userId <= 0) {
    die("User not logged in or invalid user ID.");
}

// Check if a file was uploaded
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
    // Set the target directory and file name
    $targetDir = "../uploads/";
    $fileName = basename($_FILES['profile_image']['name']);
    $targetFile = $targetDir . $fileName;
    
    // Check if the file is an image
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES['profile_image']['tmp_name']);
    if ($check !== false) {
        // Move the file to the target directory
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
            // Update the database with the new image path
            $sql = "UPDATE users SET profile_image = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $profileImagePath = 'uploads/' . $fileName;
            $stmt->bind_param('si', $profileImagePath, $userId);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "File is not an image.";
    }
} else {
    echo "No file uploaded or upload error.";
}

$conn->close();

// Redirect back to the profile page
header("Location: profilepage.php");
exit;
?>
