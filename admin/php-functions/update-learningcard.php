<?php
require_once '../../function/dbconnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_id = $_POST['card_id'];
    $card_title = $_POST['card_title'];
    $category = $_POST['category'];
    $difficulty_level = $_POST['difficulty_level'];
    $description = $_POST['description'];
    
    // Handle file upload
    $card_image = '';
    if (isset($_FILES['card_image']) && $_FILES['card_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../cards/';
        // Create directory if it doesn't exist
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
            $card_image = 'cards/' . $new_filename; // Store relative path in database
            
            // Update with new image
            $stmt = $conn->prepare("UPDATE learningcard SET card_title = ?, category = ?, difficulty_level = ?, description = ?, card_image = ? WHERE card_id = ?");
            $stmt->bind_param("sssssi", $card_title, $category, $difficulty_level, $description, $card_image, $card_id);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit();
        }
    } else {
        // Update without changing the image
        $stmt = $conn->prepare("UPDATE learningcard SET card_title = ?, category = ?, difficulty_level = ?, description = ? WHERE card_id = ?");
        $stmt->bind_param("ssssi", $card_title, $category, $difficulty_level, $description, $card_id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Card updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update card: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}