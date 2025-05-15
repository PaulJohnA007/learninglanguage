<?php
session_start();
require_once('../../function/dbconnect.php');

header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Check if card_id is provided
if (!isset($_POST['card_id'])) {
    echo json_encode(['success' => false, 'message' => 'Card ID is required']);
    exit();
}

$card_id = $_POST['card_id'];

try {
    // Start transaction
    $conn->begin_transaction();

     // Get the learning card image and subject card images
     $imageSql = "SELECT lc.card_image, GROUP_CONCAT(sc.subject_card_image) as subject_images 
     FROM learningcard lc
     LEFT JOIN subjectcard sc ON lc.card_id = sc.learning_card_id
     WHERE lc.card_id = ?
     GROUP BY lc.card_id";
$imageStmt = $conn->prepare($imageSql);
$imageStmt->bind_param("i", $card_id);
$imageStmt->execute();
$result = $imageStmt->get_result();
$images = $result->fetch_assoc();

    // Get word images before deletion
    $wordImagesSql = "SELECT w.word_image 
                      FROM wordcard w 
                      INNER JOIN subjectcard s ON w.subject_card_id = s.subject_id
                      WHERE s.learning_card_id = ? AND w.word_image IS NOT NULL AND w.word_image != ''";
    $wordImagesStmt = $conn->prepare($wordImagesSql);
    $wordImagesStmt->bind_param("i", $card_id);
    $wordImagesStmt->execute();
    $wordImagesResult = $wordImagesStmt->get_result();
    $wordImages = [];
    while ($row = $wordImagesResult->fetch_assoc()) {
        if ($row['word_image']) {
            $wordImages[] = $row['word_image'];
        }
    }

    // 1. First delete from word_progress (child of wordcard)
    $deleteProgressSql = "DELETE wp FROM word_progress wp 
                         INNER JOIN wordcard w ON wp.word_id = w.word_id 
                         INNER JOIN subjectcard s ON w.subject_card_id = s.subject_id
                         WHERE s.learning_card_id = ?";
    $progressStmt = $conn->prepare($deleteProgressSql);
    $progressStmt->bind_param("i", $card_id);
    $progressStmt->execute();

    // 2. Delete from wordcard (child of subjectcard)
    $deleteWordsSql = "DELETE w FROM wordcard w 
                       INNER JOIN subjectcard s ON w.subject_card_id = s.subject_id
                       WHERE s.learning_card_id = ?";
    $wordsStmt = $conn->prepare($deleteWordsSql);
    $wordsStmt->bind_param("i", $card_id);
    $wordsStmt->execute();

    // 3. Delete from subjectcard (child of learningcard)
    $deleteSubjectsSql = "DELETE FROM subjectcard WHERE learning_card_id = ?";
    $subjectsStmt = $conn->prepare($deleteSubjectsSql);
    $subjectsStmt->bind_param("i", $card_id);
    $subjectsStmt->execute();

    // 4. Finally delete the learning card
    $deleteCardSql = "DELETE FROM learningcard WHERE card_id = ?";
    $cardStmt = $conn->prepare($deleteCardSql);
    $cardStmt->bind_param("i", $card_id);
    $cardStmt->execute();

    // Delete learning card image
    if ($images && $images['card_image']) {
        $imageFileName = basename($images['card_image']);
        $imagePath = __DIR__ . '/../../admin/cards/' . $imageFileName;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Delete subject card images
    if ($images && $images['subject_images']) {
        $subjectImages = explode(',', $images['subject_images']);
        foreach ($subjectImages as $subjectImage) {
            if ($subjectImage) {
                $subjectImageFileName = basename($subjectImage);
                $subjectImagePath = __DIR__ . '/../../admin/subject-img/' . $subjectImageFileName;
                if (file_exists($subjectImagePath)) {
                    unlink($subjectImagePath);
                }
            }
        }
    }

    // Delete word images
    foreach ($wordImages as $wordImage) {
        if (file_exists($wordImage)) {
            unlink($wordImage);
        }
    }

    // Commit transaction
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Card and all related data deleted successfully']);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();