<?php
require_once '../../function/dbconnect.php';

header('Content-Type: application/json');

if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];
    
    $stmt = $conn->prepare("SELECT * FROM subjectcard WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($card = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'card' => $card]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Subject card not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Subject ID not provided']);
}