<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include '../../function/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_type = $_POST['subject_type'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $learning_card_id = $_POST['card_id']; // This will come from the card_id parameter

    // Handle file upload
    $subject_card_image = '';
    if (isset($_FILES['card_image']) && $_FILES['card_image']['error'] === UPLOAD_ERR_OK) {
        // Check file size - 8MB limit (8 * 1024 * 1024 bytes)
        $max_size = 8 * 1024 * 1024; // 8MB in bytes
        if ($_FILES['card_image']['size'] > $max_size) {
            echo json_encode(['success' => false, 'message' => 'Image size exceeds the 8MB limit']);
            exit();
        }
        
        $upload_dir = '../subject-img/';
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
            $subject_card_image = '.../subject-img/' . $new_filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit();
        }
    }
    
    // Insert into subjectcard table
    $stmt = $conn->prepare("INSERT INTO subjectcard (learning_card_id, subject_type, description, title, subject_card_image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $learning_card_id, $subject_type, $description, $title, $subject_card_image);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Subject card created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating subject card']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>