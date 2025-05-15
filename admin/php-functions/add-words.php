<?php
session_start();
header('Content-Type: application/json');

include '../../function/dbconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_card_id = $_POST['subject_card_id'];
    $word = $_POST['word'];
    $phonetic = $_POST['phonetic'];
    $definition = $_POST['definition'];
    $example = $_POST['example'];

    // Handle image upload
    $word_image_path = null;
    if (isset($_FILES['word_image']) && $_FILES['word_image']['error'] === UPLOAD_ERR_OK) {
        $max_file_size = 10 * 1024 * 1024; // 10MB in bytes
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

        // Validate file size
        if ($_FILES['word_image']['size'] > $max_file_size) {
            echo json_encode(['success' => false, 'message' => 'File size exceeds the maximum limit of 10MB']);
            exit();
        }

        // Validate file type
        if (!in_array($_FILES['word_image']['type'], $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, JPG, and PNG are allowed']);
            exit();
        }

        $upload_dir = '../word-img/';
        $file_name = uniqid() . '.' . pathinfo($_FILES['word_image']['name'], PATHINFO_EXTENSION);
        $word_image_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES['word_image']['tmp_name'], $word_image_path)) {
            echo json_encode(['success' => false, 'message' => 'Error uploading image']);
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO wordcard (subject_card_id, word, phonetic_spelling, definition, example_sentence, word_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $subject_card_id, $word, $phonetic, $definition, $example, $word_image_path);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Word added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding word']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>