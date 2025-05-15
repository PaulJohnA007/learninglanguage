<?php
require_once '../../function/dbconnect.php';

header('Content-Type: application/json');

if (isset($_GET['card_id'])) {
    $card_id = $_GET['card_id'];
    
    $stmt = $conn->prepare("SELECT * FROM learningcard WHERE card_id = ?");
    $stmt->bind_param("i", $card_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($card = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'card' => $card]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Card not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Card ID not provided']);
}