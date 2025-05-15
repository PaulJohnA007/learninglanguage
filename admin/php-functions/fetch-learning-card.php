<?php
session_start();
header('Content-Type: application/json');

include '../../function/dbconnect.php';

try {
    $query = "SELECT * FROM learningcard ORDER BY card_id DESC";
    $result = $conn->query($query);
    
    $cards = array();
    while ($row = $result->fetch_assoc()) {
        $cards[] = array(
            'card_id' => $row['card_id'],
            'card_title' => $row['card_title'],
            'difficulty_level' => $row['difficulty_level'],
            'category' => $row['category'],
            'card_image' => $row['card_image'],
            'description' => $row['description']
        );
    }
    
    echo json_encode(['success' => true, 'cards' => $cards]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching cards: ' . $e->getMessage()]);
}

$conn->close();
?>