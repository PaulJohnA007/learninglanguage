<?php
session_start();
header('Content-Type: application/json');

include '../../function/dbconnect.php';

try {
    $learning_card_id = isset($_GET['card_id']) ? $_GET['card_id'] : null;
    
    if (!$learning_card_id) {
        throw new Exception('No learning card ID provided');
    }

    $query = "SELECT * FROM subjectcard WHERE learning_card_id = ? ORDER BY subject_id DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $learning_card_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cards = array();
    while ($row = $result->fetch_assoc()) {
        $cards[] = $row;
    }
    
    echo json_encode(['success' => true, 'cards' => $cards]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>