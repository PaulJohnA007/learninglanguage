<?php
require_once '../../function/dbconnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'];
    $card_id = $_POST['card_id'];
    $title = $_POST['title'];
    $subject_type = $_POST['subject_type'];
    $description = $_POST['description'];
    
    // Handle file upload
    $subject_card_image = '';
    if (isset($_FILES['subject_card_image']) && $_FILES['subject_card_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../subject-img/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['subject_card_image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        // Check if file is an actual image
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type']);
            exit();
        }
        
        if (move_uploaded_file($_FILES['subject_card_image']['tmp_name'], $upload_path)) {
            $subject_card_image = '../subject-img/' . $new_filename;
            
            // Get old image path to delete
            $stmt = $conn->prepare("SELECT subject_card_image FROM subjectcard WHERE subject_id = ?");
            $stmt->bind_param("i", $subject_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($old_image = $result->fetch_assoc()) {
                if ($old_image['subject_card_image'] && file_exists('../' . $old_image['subject_card_image'])) {
                    unlink('../' . $old_image['subject_card_image']);
                }
            }
            
            // Update with new image
            $stmt = $conn->prepare("UPDATE subjectcard SET title = ?, subject_type = ?, description = ?, subject_card_image = ? WHERE subject_id = ?");
            $stmt->bind_param("ssssi", $title, $subject_type, $description, $subject_card_image, $subject_id);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit();
        }
    } else {
        // Update without changing the image
        $stmt = $conn->prepare("UPDATE subjectcard SET title = ?, subject_type = ?, description = ? WHERE subject_id = ?");
        $stmt->bind_param("sssi", $title, $subject_type, $description, $subject_id);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Subject card updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to update subject card: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method'
    ]);
}