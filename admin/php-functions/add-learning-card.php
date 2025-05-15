<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
} 
include '../../function/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_title = $_POST['title'];
    $difficulty_level = $_POST['level'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    
    // Handle file upload
$card_image = '';
if (isset($_FILES['card_image']) && $_FILES['card_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../cards/'; // Physical upload directory
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['card_image']['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    // Check if file is an actual image
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        exit();
    }
    
    if (move_uploaded_file($_FILES['card_image']['tmp_name'], $upload_path)) {
        $card_image = 'cards/' . $new_filename; // Store path without ../
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
        exit();
    }
}
    
    // Prepare and execute the SQL query
    $stmt = $conn->prepare("INSERT INTO learningcard (card_title, difficulty_level, category, card_image, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $card_title, $difficulty_level, $category, $card_image, $description);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Learning card created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating learning card']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>