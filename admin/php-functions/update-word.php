<?php
require_once '../../function/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $word_id = $_POST['word_id'];
    $word = $_POST['word'];
    $phonetic_spelling = $_POST['phonetic_spelling'];
    $definition = $_POST['definition'];
    $example_sentence = $_POST['example_sentence'];
    $word_image = isset($_FILES['word_image']) ? $_FILES['word_image'] : null;

    // Fetch the current image path from the database
    $stmt = $conn->prepare("SELECT word_image FROM wordcard WHERE word_id = ?");
    $stmt->bind_param("i", $word_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentImage = $result->fetch_assoc()['word_image'];
    $stmt->close();

    // Handle image upload if a new image is provided
    $newImagePath = $currentImage; // Default to the current image
    if ($word_image && $word_image['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../word-img/';
        $newImageName = uniqid() . '-' . basename($word_image['name']);
        $newImagePath = $uploadDir . $newImageName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($word_image['tmp_name'], $newImagePath)) {
            // Delete the old image file if it exists
            if ($currentImage && file_exists('../admin/' . $currentImage)) {
                unlink('../admin/' . $currentImage);
            }

            // Update the image path to store in the database
            $newImagePath = '../word-img/' . $newImageName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Error uploading new image']);
            exit;
        }
    }

    // Update the word details in the database
    $stmt = $conn->prepare("UPDATE wordcard SET word = ?, phonetic_spelling = ?, definition = ?, example_sentence = ?, word_image = ? WHERE word_id = ?");
    $stmt->bind_param("sssssi", $word, $phonetic_spelling, $definition, $example_sentence, $newImagePath, $word_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Word updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating word']);
    }

    $stmt->close();
}