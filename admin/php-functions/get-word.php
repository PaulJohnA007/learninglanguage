<?php
require_once '../../function/dbconnect.php';

if (isset($_GET['word_id'])) {
    $word_id = $_GET['word_id'];
    
    $stmt = $conn->prepare("SELECT * FROM wordcard WHERE word_id = ?");
    $stmt->bind_param("i", $word_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($word = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'word' => $word]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Word not found']);
    }
}