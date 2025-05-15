<?php
session_start();
require_once('../../function/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Fetch learning cards ordered by card_id
$sql = "SELECT * FROM learningcard ORDER BY card_id ASC";
$result = $conn->query($sql);
$cardsData = [];

// First pass: fetch all card data and progress
while ($card = $result->fetch_assoc()) {
    // Get total words
    $totalWordsSql = "SELECT COUNT(DISTINCT word_id) as total 
                      FROM wordcard w
                      INNER JOIN subjectcard s ON w.subject_card_id = s.subject_id
                      WHERE s.learning_card_id = ?";
    $totalWordsStmt = $conn->prepare($totalWordsSql);
    $totalWordsStmt->bind_param("i", $card['card_id']);
    $totalWordsStmt->execute();
    $totalWords = $totalWordsStmt->get_result()->fetch_assoc()['total'];

    // Get completed words
    $completedWordsSql = "SELECT COUNT(DISTINCT wp.word_id) as completed 
                          FROM word_progress wp
                          INNER JOIN wordcard w ON wp.word_id = w.word_id
                          INNER JOIN subjectcard s ON w.subject_card_id = s.subject_id
                          WHERE wp.user_id = ? 
                          AND s.learning_card_id = ?
                          AND wp.completed = 1";
    $completedStmt = $conn->prepare($completedWordsSql);
    $completedStmt->bind_param("ii", $_SESSION['user_id'], $card['card_id']);
    $completedStmt->execute();
    $completedWords = $completedStmt->get_result()->fetch_assoc()['completed'];

    // Calculate progress
    $progress = $totalWords > 0 ? round(($completedWords / $totalWords) * 100) : 0;

    // Store card data with progress
    $card['total_words'] = $totalWords;
    $card['completed_words'] = $completedWords;
    $card['progress'] = $progress;
    $cardsData[] = $card;
}

// Second pass: determine lock status based on cumulative progress
$finalCards = [];
$accumulatedProgress = 0;

foreach ($cardsData as $index => $card) {
    $cardGradeLevel = $index + 1;
    $isLocked = true; // Default to locked

    // echo "Card {$card['card_title']}-Total Words: {$card['total_words']}|Completed Words: {$card['completed_words']}|Progress: {$card['progress']}%<br>";
    // echo "Previous Cards Total Progress: {$accumulatedProgress}<br>";

    if ($cardGradeLevel === 1) {
        // Card 1 is always unlocked
        $isLocked = false;
    } elseif ($cardGradeLevel === 2) {
        // Card 2 unlocks if user's grade level is at least 2
        $isLocked = ($_SESSION['grade_level'] >= 2 || $accumulatedProgress >= 100) ? false : true;
    } elseif ($cardGradeLevel === 3) {
        // Card 3 unlocks if grade level is at least 3 OR previous cards are completed
        $isLocked = ($_SESSION['grade_level'] >= 3 || $accumulatedProgress >= 200) ? false : true;
    }
    elseif ($cardGradeLevel === 4) {
        // Card 4 unlocks if grade level is at least 4 OR previous cards are completed
        $isLocked = ($_SESSION['grade_level'] >= 4 || $accumulatedProgress >= 300) ? false : true;
    }
    elseif ($cardGradeLevel === 5) {
        // Card 5 unlocks if grade level is at least 5 OR previous cards are completed
        $isLocked = ($_SESSION['grade_level'] >= 5 || $accumulatedProgress >= 400) ? false : true;
    }
    elseif ($cardGradeLevel === 6) {
        // Card 6 unlocks if grade level is at least 6 OR previous cards are completed
        $isLocked = ($_SESSION['grade_level'] >= 6 || $accumulatedProgress >= 500) ? false : true;
    }

    $card['is_locked'] = $isLocked;
    $finalCards[] = $card;

    // echo "Card {$card['card_title']}-Is Locked: " . ($isLocked ? 'Yes' : 'No') . "<br>";

    // Add this card's progress to accumulated progress BEFORE next iteration
    if ($card['progress'] == 100) {
        $accumulatedProgress += 100;
    }

    // echo "Updated Previous Card Progress: {$accumulatedProgress}<br>";
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'cards' => $finalCards,
    'user_grade' => $_SESSION['grade_level']
]);