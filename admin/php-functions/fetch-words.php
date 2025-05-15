<?php
session_start();
header('Content-Type: application/json');

include '../../function/dbconnect.php';

if (isset($_GET['subject_card_id'])) {
    $subject_card_id = $_GET['subject_card_id'];
    
    $stmt = $conn->prepare("SELECT * FROM wordcard WHERE subject_card_id = ? ORDER BY word_id DESC");
    $stmt->bind_param("i", $subject_card_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $words = [];
    
    while ($row = $result->fetch_assoc()) {
        $words[] = $row;
    }
    
    echo json_encode(['success' => true, 'words' => $words]);
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No subject card ID provided']);
}

$conn->close();
?>