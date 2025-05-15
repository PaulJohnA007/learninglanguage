<?php
header('Content-Type: application/json');

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['transcript']) || !isset($data['expectedWord']) || !isset($data['confidence'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing required parameters'
    ]);
    exit;
}

$transcript = strtolower(trim($data['transcript']));
$expectedWord = strtolower(trim($data['expectedWord']));
$confidence = floatval($data['confidence']);
$wordId = isset($data['wordId']) ? intval($data['wordId']) : 0;

// Evaluate the pronunciation
$result = evaluatePronunciation($transcript, $expectedWord, $confidence);

echo json_encode([
    'success' => true,
    'finalScore' => $result['finalScore'],
    'pronunciationScore' => $result['pronunciationScore'],
    'adjustedConfidence' => $result['adjustedConfidence'],
    'pronunciationWeight' => $result['pronunciationWeight'],
    'wordId' => $wordId,
    'transcript' => $transcript,
    'expectedWord' => $expectedWord,
    'feedbackMessage' => $result['feedbackMessage'] ?? '',
    'vowelMatchScore' => $result['vowelMatchScore'] ?? 0
]);

/**
 * Soundex algorithm implementation
 */
function soundex_custom($word) {
    if (!$word || !is_string($word)) return '';
    
    // Convert to uppercase and keep only letters
    $word = strtoupper(preg_replace('/[^A-Z]/i', '', $word));
    
    if (!$word) return '';
    
    // Keep first letter
    $firstLetter = $word[0];
    
    $mapping = [
        'B' => 1, 'F' => 1, 'P' => 1, 'V' => 1,
        'C' => 2, 'G' => 2, 'J' => 2, 'K' => 2, 'Q' => 2, 'S' => 2, 'X' => 2, 'Z' => 2,
        'D' => 3, 'T' => 3,
        'L' => 4,
        'M' => 5, 'N' => 5,
        'R' => 6
    ];
    
    // Replace consonants with digits
    $code = '';
    for ($i = 1; $i < strlen($word); $i++) {
        $ch = $word[$i];
        if (isset($mapping[$ch]) && !in_array($ch, ['A', 'E', 'I', 'O', 'U', 'H', 'W', 'Y'])) {
            $code .= $mapping[$ch];
        }
    }
    
    // Remove adjacent duplicates
    if (strlen($code) > 0) {
        $newCode = $code[0];
        for ($i = 1; $i < strlen($code); $i++) {
            if ($code[$i] !== $code[$i-1]) {
                $newCode .= $code[$i];
            }
        }
        $code = $newCode;
    }
    
    // Ensure the result is exactly 3 digits (plus the first letter)
    return $firstLetter . str_pad(substr($code, 0, 3), 3, '0');
}

/**
 * Evaluates pronunciation based on transcript and expected word
 */
function evaluatePronunciation($spokenText, $targetWord, $confidence) {
    // Handle empty inputs
    if (empty($spokenText) || empty($targetWord)) {
        return [
            'pronunciationScore' => 0,
            'adjustedConfidence' => 0,
            'pronunciationWeight' => 0.9,
            'finalScore' => 0
        ];
    }
    
    // Clean and normalize both inputs
    $spoken = strtolower(trim(preg_replace('/[.,\/#!$%\^&\*;:{}=\-_`~()]/','', $spokenText)));
    $spoken = preg_replace('/\s{2,}/', ' ', $spoken);
    
    $target = strtolower(trim(preg_replace('/[.,\/#!$%\^&\*;:{}=\-_`~()]/','', $targetWord)));
    $target = preg_replace('/\s{2,}/', ' ', $target);
    
    // Initialize scoring variables
    $maxScore = 100;
    
    // Check for exact match first (fastest path)
    if ($spoken === $target) {
        return [
            'pronunciationScore' => $maxScore,
            'adjustedConfidence' => $confidence * 100,
            'pronunciationWeight' => 0.7,
            'finalScore' => 100
        ];
    }
    
    // Use soundex for phonetic matching of consonants
    $spokenSoundex = soundex_custom($spoken);
    $targetSoundex = soundex_custom($target);
    
    // If soundex codes are completely different, apply a heavy penalty
    if ($spokenSoundex[0] !== $targetSoundex[0]) {
        return [
            'pronunciationScore' => 0,
            'adjustedConfidence' => min($confidence * 100, 50),
            'pronunciationWeight' => 0.9,
            'finalScore' => 0,
            'feedbackMessage' => 'That sounds like a different word. Try again.'
        ];
    }
    
    // Extract vowels
    preg_match_all('/[aeiou]/', $target, $targetVowelsMatches);
    preg_match_all('/[aeiou]/', $spoken, $spokenVowelsMatches);
    
    $targetVowels = $targetVowelsMatches[0] ?? [];
    $spokenVowels = $spokenVowelsMatches[0] ?? [];
    
    // Calculate vowel match score
    $vowelMatchScore = 0;
    if (count($targetVowels) > 0 && count($spokenVowels) > 0) {
        // Check difference in vowel count
        $vowelCountDiff = abs(count($targetVowels) - count($spokenVowels));
        $vowelCountPenalty = $vowelCountDiff * 10; // 10% penalty per extra/missing vowel
        
        // Count matching vowels (in order)
        $matchingVowels = 0;
        $minVowels = min(count($targetVowels), count($spokenVowels));
        
        for ($i = 0; $i < $minVowels; $i++) {
            if ($targetVowels[$i] === $spokenVowels[$i]) {
                $matchingVowels++;
            }
        }
        
        // Calculate base score from matching vowels
        $baseVowelScore = round(($matchingVowels / count($targetVowels)) * 100);
        
        // Apply penalty for different vowel counts
        $vowelMatchScore = max(0, $baseVowelScore - $vowelCountPenalty);
    }
    
    // If soundex codes match, check if vowels also match well
    if ($spokenSoundex === $targetSoundex) {
        // For words with different vowel patterns despite matching Soundex
        if ($vowelMatchScore < 70) {
            $adjustedScore = round($vowelMatchScore * 0.7 + 30); // Maximum 79% for bad vowel matching
            
            return [
                'pronunciationScore' => $adjustedScore,
                'adjustedConfidence' => $confidence * 100,
                'pronunciationWeight' => 0.7,
                'finalScore' => $adjustedScore,
                'feedbackMessage' => 'Try to focus on the vowel sounds.',
                'vowelMatchScore' => $vowelMatchScore
            ];
        }
        
        // Good vowel match and matching Soundex - high score
        return [
            'pronunciationScore' => 95,
            'adjustedConfidence' => $confidence * 100,
            'pronunciationWeight' => 0.7,
            'finalScore' => round((95 * 0.7) + ($confidence * 100 * 0.3)),
            'vowelMatchScore' => $vowelMatchScore
        ];
    }
    
    // For non-exact soundex match, check how similar they are
    $soundexSimilarity = 0;
    for ($i = 0; $i < 4; $i++) {
        if (isset($spokenSoundex[$i]) && isset($targetSoundex[$i]) && $spokenSoundex[$i] === $targetSoundex[$i]) {
            $soundexSimilarity += 25; // Each matching position is worth 25%
        }
    }
    
    // Check for close matches (like "cats" for "cat")
    if (strpos($spoken, $target) === 0 || strpos($target, $spoken) === 0) {
        $lengthDiff = abs(strlen($spoken) - strlen($target));
        if ($lengthDiff <= 2) { // Allow small differences like plural forms
            $closeMatchScore = max(85, $maxScore - ($lengthDiff * 5));
            
            return [
                'pronunciationScore' => $closeMatchScore,
                'adjustedConfidence' => $confidence * 100,
                'pronunciationWeight' => 0.7,
                'finalScore' => round(($closeMatchScore * 0.7) + ($confidence * 100 * 0.3)),
                'vowelMatchScore' => $vowelMatchScore
            ];
        }
    }
    
    // For single-word targets, see if the spoken text contains the target word
    if (strpos($target, ' ') === false && strpos($spoken, $target) !== false) {
        return [
            'pronunciationScore' => 90,
            'adjustedConfidence' => $confidence * 100,
            'pronunciationWeight' => 0.7,
            'finalScore' => round((90 * 0.7) + ($confidence * 100 * 0.3)),
            'vowelMatchScore' => $vowelMatchScore
        ];
    }
    
    // Use Levenshtein distance for more complex comparison
    $distance = levenshtein($spoken, $target);
    $maxLength = max(strlen($spoken), strlen($target));
    
    // Calculate basic score based on edit distance
    $levenshteinScore = max(0, round((1 - $distance / $maxLength) * 100));
    
    // Combine all signals: soundex similarity (40%), vowel match (30%), edit distance (30%)
    $pronunciationScore = round(
        ($soundexSimilarity * 0.4) + 
        ($vowelMatchScore * 0.3) + 
        ($levenshteinScore * 0.3)
    );
    
    // Dynamic weighting based on word characteristics and pronunciation score
    $pronunciationWeight = 0.7; // Default weight
    $feedbackMessage = '';

    // For very poor matches, increase the pronunciation weight significantly
    if ($pronunciationScore <= 30) {
        $pronunciationWeight = 0.9; // Heavily weight pronunciation for bad matches
    } else {
        // Normal weighting rules for better matches
        // Adjust weights based on word length
        if (strlen($target) <= 3) {
            $pronunciationWeight = 0.8;  // Give more weight to pronunciation for short words
        }
        
        // Adjust weights for words with difficult sounds
        $difficultSounds = ['th', 'sh', 'ch', 'j', 'v', 'z', 'wh'];
        foreach ($difficultSounds as $sound) {
            if (strpos($target, $sound) !== false) {
                $pronunciationWeight = max(0.6, $pronunciationWeight - 0.1);  // Slightly more lenient
                break;
            }
        }
    }
    
    // Adjust confidence based on pronunciation score
    $adjustedConfidence = $confidence * 100;

    // For very poor pronunciation matches, reduce the confidence
    if ($pronunciationScore <= 30) {
        // Scale down confidence for completely different words
        $adjustedConfidence = min($adjustedConfidence, 50);
    }

    // Adjust score based on word length
    if (strlen($target) <= 3) {
        // For very short words, be more strict
        $pronunciationScore = round($pronunciationScore * 0.9); // 10% penalty for short words
    } else if (strlen($target) >= 8) {
        // For longer words or phrases, be more lenient
        $pronunciationScore = min(100, round($pronunciationScore * 1.1)); // 10% bonus for long words
    }
    
    // Calculate final score
    $finalScore = round(
        ($pronunciationScore * $pronunciationWeight) + 
        ($adjustedConfidence * (1 - $pronunciationWeight))
    );
    
    // Add specific feedback for common pronunciation issues
    if ($pronunciationScore < 95 && $spoken !== $target) {
        // Generate feedback based on specific differences
        if (strpos($target, 'th') !== false && strpos($spoken, 'th') === false) {
            $feedbackMessage = "Tip: Focus on the 'th' sound in \"$target\"";
        } else if (strpos($target, 'r') !== false && strpos($spoken, 'r') === false) {
            $feedbackMessage = "Tip: Try to pronounce the 'r' sound more clearly";
        } else if ($vowelMatchScore < 70) {
            $feedbackMessage = "Tip: Pay attention to the vowel sounds";
        }
    }
    
    return [
        'pronunciationScore' => $pronunciationScore,
        'adjustedConfidence' => $adjustedConfidence,
        'pronunciationWeight' => $pronunciationWeight,
        'finalScore' => $finalScore,
        'feedbackMessage' => $feedbackMessage,
        'vowelMatchScore' => $vowelMatchScore
    ];
}
?>