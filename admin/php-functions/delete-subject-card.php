
<?php
require_once '../../function/dbconnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_id'])) {
    $subject_id = $_POST['subject_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First, get the image path to delete the file
        $stmt = $conn->prepare("SELECT subject_card_image FROM subjectcard WHERE subject_id = ?");
        $stmt->bind_param("i", $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($card = $result->fetch_assoc()) {
            // Delete the image file if it exists
            if ($card['subject_card_image']) {
                $image_path = str_replace('.../', '../', $card['subject_card_image']);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        
        // Get all word IDs associated with this subject card
        $word_stmt = $conn->prepare("SELECT word_id FROM wordcard WHERE subject_card_id = ?");
        $word_stmt->bind_param("i", $subject_id);
        $word_stmt->execute();
        $word_result = $word_stmt->get_result();
        
        $word_ids = [];
        while ($row = $word_result->fetch_assoc()) {
            $word_ids[] = $row['word_id'];
        }
        
        // If there are word IDs, delete associated records from word_progress first
        if (!empty($word_ids)) {
            // Convert array to comma-separated string for the IN clause
            $word_ids_str = implode(',', $word_ids);
            
            // Delete from word_progress table
            $progress_stmt = $conn->prepare("DELETE FROM word_progress WHERE word_id IN ($word_ids_str)");
            $progress_stmt->execute();
            $progress_stmt->close();
            
            // Log how many progress records were deleted
            $progress_count = $conn->affected_rows;
            error_log("Deleted $progress_count progress records related to subject $subject_id");
        }
        
        // Delete associated words
        $word_delete_stmt = $conn->prepare("DELETE FROM wordcard WHERE subject_card_id = ?");
        $word_delete_stmt->bind_param("i", $subject_id);
        $word_delete_stmt->execute();
        $words_count = $conn->affected_rows;
        $word_delete_stmt->close();
        
        // Then delete the subject card
        $subject_stmt = $conn->prepare("DELETE FROM subjectcard WHERE subject_id = ?");
        $subject_stmt->bind_param("i", $subject_id);
        $subject_stmt->execute();
        $subject_stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => "Subject card deleted successfully with $words_count associated words"
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting subject card: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}