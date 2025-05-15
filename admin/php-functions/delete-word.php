<?php
require_once '../../function/dbconnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['word_id'])) {
    $word_id = $_POST['word_id'];
    
    // Start a transaction to ensure both operations succeed or fail together
    $conn->begin_transaction();
    
    try {
        // First get the image path before deleting the word
        $image_stmt = $conn->prepare("SELECT word_image FROM wordcard WHERE word_id = ?");
        $image_stmt->bind_param("i", $word_id);
        $image_stmt->execute();
        $result = $image_stmt->get_result();
        $word_data = $result->fetch_assoc();
        $image_stmt->close();

        // Delete the image file if it exists
        if ($word_data && !empty($word_data['word_image'])) {
            $image_path = $word_data['word_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Delete from word_progress table
        $progress_stmt = $conn->prepare("DELETE FROM word_progress WHERE word_id = ?");
        $progress_stmt->bind_param("i", $word_id);
        $progress_stmt->execute();
        $progress_stmt->close();
        
        // Then delete from wordcard table
        $word_stmt = $conn->prepare("DELETE FROM wordcard WHERE word_id = ?");
        $word_stmt->bind_param("i", $word_id);
        
        if (!$word_stmt->execute()) {
            throw new Exception("Failed to delete word: " . $conn->error);
        }
        
        $word_stmt->close();
        
        // If we got here, commit the transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Word and associated image deleted successfully'
        ]);
    } catch (Exception $e) {
        // If there was an error, roll back the transaction
        $conn->rollback();
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}