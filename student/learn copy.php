<?php
session_start();
require_once('../function/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: ../loginsignup_page.php");
  exit();
}

// Get the card_id from URL parameter
$card_id = isset($_GET['card_id']) ? (int) $_GET['card_id'] : 0;

// Fetch subject cards for the selected learning card
$sql = "SELECT * FROM subjectcard WHERE learning_card_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $card_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the learning card details
$cardSql = "SELECT * FROM learningcard WHERE card_id = ?";
$cardStmt = $conn->prepare($cardSql);
$cardStmt->bind_param("i", $card_id);
$cardStmt->execute();
$cardResult = $cardStmt->get_result();
$learningCard = $cardResult->fetch_assoc();

// Prepare word statement
$wordSql = "SELECT * FROM wordcard WHERE subject_card_id = ?";
$wordStmt = $conn->prepare($wordSql);

// Function to get words for a subject card
function getWords($subjectId, $stmt)
{
  $sql = "SELECT w.* 
          FROM wordcard w 
          LEFT JOIN word_progress wp ON w.word_id = wp.word_id 
              AND wp.user_id = ? 
              AND wp.completed = 1
          WHERE w.subject_card_id = ? 
          AND wp.word_id IS NULL 
          LIMIT 1";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("ii", $_SESSION['user_id'], $subjectId);
  $stmt->execute();
  $result = $stmt->get_result();
  $words = $result->fetch_assoc();

  // If no words found (all completed), return false
  return $words ? $words : false;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
  <link rel="stylesheet" href="../assets/css/learn.css">
</head>

<style>
  .fixed-img {
    width: 100%;
    height: 150px;
    object-fit: cover;
  }

  .progressbar {
    width: 100%;
  }

  /* Add a glow effect to the progress bar when it's filling */
  .bg-blue-600 {
    box-shadow: 0 0 8px rgba(37, 99, 235, 0.5);
  }

  /* Make text more visible on the gradient backgrounds */
  .text-gray-600 {
    font-weight: 500;
    text-shadow: 0 0 2px rgba(255, 255, 255, 0.5);
  }

  @keyframes shimmer {
    0% {
      transform: translateX(-100%);
    }

    100% {
      transform: translateX(100%);
    }
  }

  .animate-shimmer {
    background: linear-gradient(90deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.3) 50%,
        rgba(255, 255, 255, 0) 100%);
    animation: shimmer 2s infinite;
  }
</style>

<body>
  <div class="min-h-screen flex flex-col md:flex-row relative">
    <!-- Burger Menu Icon -->
    <div class="md:hidden flex justify-between p-1 text-white bg-[#0038A8]">
      <span class="text-2xl text-center font-bold">DASHBOARD</span>
      <button id="burgerMenu" class="bg-[#0038A8] text-3xl focus:outline-none">
        &#9776;
      </button>
    </div>

    <a href="dashboardselect.php" class="absolute top-4 right-4 bg-[#58CC02] hover:bg-[#46a302] text-white px-4 py-2 rounded-xl 
              flex items-center gap-2 transform transition-all duration-300 hover:scale-105 shadow-lg">
      <span>🏠</span>
      <span class="font-bold">Back to Home</span>
    </a>
    <!-- Main Content -->
    <div
      class="min-h-screen bg-gradient-to-r from-purple-400 to-blue-500 flex flex-col items-center justify-center p-4 space-y-6 flex-1 bg-cover bg-center"
      style="background-image: url('../pics/back1.jpg');">
      <div
        class="bg-yellow-500 rounded-3xl shadow-2xl p-2 w-full max-w-sm text-center transform transition-transform duration-500 hover:scale-105 animate-bounce">
        <h1 class="text-2xl font-bold text-primary">
          <?php echo htmlspecialchars($learningCard['card_title']); ?>
        </h1>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full max-w-3xl">

        <?php
        // Store subjects in an array first
        $subjects = [];
        while ($subject = $result->fetch_assoc()) {
          $subjects[] = $subject;
        }

        // Use the stored subjects array for both the cards and modals
        

        function getSubjectProgress($subjectId, $conn)
        {
          // Get total words count
          $totalSql = "SELECT COUNT(*) as total FROM wordcard WHERE subject_card_id = ?";
          $totalStmt = $conn->prepare($totalSql);
          $totalStmt->bind_param("i", $subjectId);
          $totalStmt->execute();
          $totalResult = $totalStmt->get_result();
          $totalRow = $totalResult->fetch_assoc();
          $totalWords = $totalRow['total'];

          if ($totalWords == 0)
            return 0;

          // Get completed words count
          $completedSql = "SELECT COUNT(*) as completed FROM word_progress 
                    WHERE word_id IN (SELECT word_id FROM wordcard WHERE subject_card_id = ?) 
                    AND user_id = ? AND completed = 1";
          $completedStmt = $conn->prepare($completedSql);
          $completedStmt->bind_param("ii", $subjectId, $_SESSION['user_id']);
          $completedStmt->execute();
          $completedResult = $completedStmt->get_result();
          $completedRow = $completedResult->fetch_assoc();
          $completedWords = $completedRow['completed'];

          // Calculate percentage
          $progress = ($completedWords / $totalWords) * 100;
          return $progress;
        }

        function getCompletedWords($subjectId, $conn)
        {
          $sql = "SELECT w.word FROM wordcard w
          JOIN word_progress wp ON w.word_id = wp.word_id
          WHERE w.subject_card_id = ? AND wp.user_id = ? AND wp.completed = 1";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("ii", $subjectId, $_SESSION['user_id']);
          $stmt->execute();
          $result = $stmt->get_result();
          $completedWords = [];
          while ($row = $result->fetch_assoc()) {
            $completedWords[] = $row['word'];
          }
          return $completedWords;
        }


        // Then in your subjects loop, call this function for each subject:
        foreach ($subjects as $subject):
          $subjectId = $subject['subject_id'];
          $progress = getSubjectProgress($subjectId, $conn);
          ?>
          <!-- Update the subject card div class -->
          <div id="<?php echo $subject['subject_type']; ?>Section" data-subject-id="<?php echo $subject['subject_id']; ?>"
            class="cursor-pointer bg-white rounded-2xl p-4 flex flex-col items-center transform 
     transition-transform duration-500 hover:rotate-6 hover:scale-105 border-2 border-[#58CC02]">

            <img class="rounded-2xl fixed-img"
              src="../admin/subject-img/<?php echo basename($subject['subject_card_image']); ?>"
              alt="<?php echo htmlspecialchars($subject['title']); ?>" class="mb-4">

            <h3 class="text-lg font-semibold text-gray-800">
              <?php echo htmlspecialchars($subject['title']); ?>
            </h3>

            <p class="text-sm text-gray-600">
              <?php echo htmlspecialchars($subject['description']); ?>
            </p>

            <!-- Update progress bar colors -->
            <div class="mt-4 progressbar w-full group relative">
              <div class="bg-gray-200 rounded-full h-3 cursor-pointer overflow-hidden">
                <div class="bg-[#58CC02] h-full rounded-full transition-all duration-500 relative"
                  style="width: <?php echo round($progress); ?>%">
                  <div class="absolute inset-0 bg-white/20 animate-shimmer"></div>
                </div>
              </div>
              <p class="text-sm text-gray-600 mt-1">Progress: <?php echo round($progress); ?>%</p>
            </div>
          </div>

        <?php endforeach; ?>

        <?php foreach ($subjects as $subject):
          $words = getWords($subject['subject_id'], $wordStmt);
          $subjectId = $subject['subject_id']; // Store for easier reference
          ?>
          <div id="modal_<?php echo $subjectId; ?>" class="modal">
            <div class="modal-content">
              <span class="close-button">×</span>
              <div class="modal-header">
                <h2 class="modal-title"><?php echo strtoupper($subject['title']); ?> SECTION</h2>
              </div>
              <?php if ($words): ?>
                <!-- Show word content if words exist -->
                <div class="modal-body">
                  <div class="modal-content-center">
                    <h2 class="text-xl font-bold mb-2">SAY THE FOLLOWING WORD:</h2>
                    <div class="text-lg font-bold" style="font-size: 35px;" id="dynamicWord_<?php echo $subjectId; ?>">
                      <?php echo htmlspecialchars($words['word']); ?>
                    </div>
                  </div>
                  <div class="modal-content-left">
                    <div class="text-lg font-semibold">SOUNDS LIKE:</div>
                    <div class="text-lg" id="phoneticTranscription_<?php echo $subjectId; ?>">
                      <?php echo htmlspecialchars($words['phonetic_spelling']); ?>
                      <span id="speakButton_<?php echo $subjectId; ?>"
                        class="inline-block ml-2 cursor-pointer speakButton">🔊</span>
                    </div>
                  </div>
                  <div class="modal-content-bottom">
                    <div class="text-lg font-semibold">Example:</div>
                    <div class="text-lg italic">
                      <?php echo htmlspecialchars($words['example_sentence']); ?>
                    </div>
                    <div class="text-lg">
                      <span id="micButton_<?php echo $subjectId; ?>"
                        class="inline-block ml-2 mt-4 cursor-pointer micButton">🔈</span>
                    </div>
                  </div>
                  <div class="mic-animation hidden" id="micAnimation_<?php echo $subjectId; ?>">
                    <div class="mic-pulse"></div>
                    <div class="mic-status">Listening...</div>
                  </div>
                </div>
              <?php else: ?>
                <!-- Show completion message if no words left -->
                <div class="modal-body">
                  <div class="completion-message text-center py-8">
                    <div class="text-4xl mb-4">🎉</div>
                    <h2 class="text-2xl font-bold text-green-600 mb-4">Congratulations!</h2>
                    <p class="text-lg mb-4">You've completed all words in this section!</p>
                    <a href="dashboardselect.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg 
                      transition-colors duration-300 inline-block">
                      Back to Dashboard
                    </a>
                  </div>

                  <?php
                  // Fetch completed words for the specific subject
                  $completedWords = getCompletedWords($subjectId, $conn);
                  if (!empty($completedWords)): ?>
                    <!-- Completed Words Section -->
                    <div class="completed-words-section mt-6">
                      <h3 class="text-xl font-bold text-gray-700 mb-4">Words You've Completed in
                        <?php echo htmlspecialchars($subject['title']); ?>:
                      </h3>
                      <ul class="list-disc list-inside text-lg text-gray-600">
                        <?php foreach ($completedWords as $index => $word): ?>
                          <li class="flex items-center">
                            <!-- Add a unique ID for each word -->
                            <span id="dynamicWordCompleted_<?php echo $subjectId; ?>_<?php echo $index; ?>" class="mr-2">
                              <?php echo htmlspecialchars($word); ?>
                            </span>
                            <!-- Add a unique speaking button -->
                            <span id="speakButtonCompleted_<?php echo $subjectId; ?>_<?php echo $index; ?>"
                              class="inline-block ml-2 cursor-pointer speakButtonCompleted">🔊</span>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  <?php endif; ?>



                </div>
              <?php endif; ?>


            </div>
          </div>
        <?php endforeach; ?>


      </div>
      <!-- Update the modal content -->
      <?php while ($subject = $result->fetch_assoc()):
        $words = getWords($subject['subject_id'], $wordStmt);
        ?>
        <div id="modal_<?php echo $subject['subject_id']; ?>" class="modal">
          <div class="modal-content">
            <div class="carousel-container">
              <!-- Navigation Arrows -->
              <button class="carousel-arrow prev">🦋</button>
              <button class="carousel-arrow next">🦋</button>

              <!-- Card Content -->
              <div class="carousel-card">
                <div class="card-front">
                  <div class="word-display">
                    <h2 id="dynamicWord" class="bounce-text"></h2>
                    <div class="word-decorations">
                      <span class="sparkle">✨</span>
                      <span class="star">⭐</span>
                      <span class="sparkle">✨</span>
                    </div>
                  </div>

                  <div class="interaction-buttons">
                    <button class="sound-btn">
                      <span class="emoji-icon">🔊</span>
                      <span class="btn-text">Listen!</span>
                    </button>
                    <button class="speak-btn">
                      <span class="emoji-icon">🎤</span>
                      <span class="btn-text">Speak!</span>
                    </button>
                  </div>

                  <div class="progress-wrapper">
                    <div class="progress-stars">
                      ⭐ ⭐ ⭐ ⭐ ⭐
                    </div>
                    <div class="progress-bar-container">
                      <div class="progress-bar-animated"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php endwhile; ?>

      <script>

      </script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <script>
        let recognitionInstance = null;
        let isRecording = false;

        // block 1 //
        const subjectData = {};

        document.addEventListener('DOMContentLoaded', function () {
          const sections = document.querySelectorAll('[id$="Section"]');

          sections.forEach(section => {
            section.addEventListener('click', function () {
              const subjectId = this.dataset.subjectId;
              const modal = document.getElementById(`modal_${subjectId}`);


              // Fetch words for this subject when modal opens
              fetch(`get_words.php?subject_id=${subjectId}`)
                .then(response => {
                  console.log(`Fetching words for subject ${subjectId}, status:`, response.status);
                  return response.json();
                })
                .then(data => {
                  // console.log(`Received ${data.length} words for subject ${subjectId}:`, data);

                  // Check if we already have progress for this subject
                  const existingData = subjectData[subjectId];

                  // Store words for this specific subject
                  subjectData[subjectId] = {
                    words: data,
                    currentIndex: existingData ? existingData.currentIndex : 0
                  };

                  // console.log('Updated subject data:', subjectData[subjectId]);

                  // For backward compatibility, also set global variables
                  window.words = data;
                  window.currentWordIndex = existingData ? existingData.currentIndex : 0;

                  if (modal) {
                    modal.style.display = 'flex';
                  }
                })
                .catch(error => {
                  console.error('Error fetching words:', error);
                  Swal.fire({
                    title: 'Error!',
                    text: 'Could not load words for this section. Please try again.',
                    icon: 'error'
                  });
                });
            });
          });
        });
        // block 1 //

        // block 2 //
        document.getElementById('burgerMenu').addEventListener('click', function () {
          const sidebar = document.getElementById('sidebar');
          if (sidebar.style.maxHeight) {
            sidebar.style.maxHeight = null;
          } else {
            sidebar.style.maxHeight = sidebar.scrollHeight + 'px';
          }
        });
        // block 2 //

        // block 3 //
        document.querySelectorAll('.menu-item').forEach(item => {
          item.addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.style.maxHeight = null;
          });
        });
        // block 3 //


        // block 4 //
        // Mapping of words to phonetic transcriptions
        const phoneticMap = {
          'Mathematics': 'maTH(ə)ˈmadiks/',
          'English': '/ˈɪŋɡlɪʃ/'
          // Add more mappings as needed
        };
        // block 4 //


        // block 5 //
        // Function to update the word and phonetic transcription
        function updateContent(newWord) {
          // Update the word
          document.getElementById('dynamicWord').textContent = newWord;

          // Update the phonetic transcription based on the new word
          const phoneticTranscription = phoneticMap[newWord] || 'Phonetic transcription not found';
          document.getElementById('phoneticTranscription').innerHTML = phoneticTranscription +
            ' <span id="speakButton" class="inline-block ml-2">&#128266;</span>';
        }
        // block 5 //


        // block 6 //
        // Function to speak the text
        function speakText(text) {
          console.log('Speaking text:', text);
          const utterance = new SpeechSynthesisUtterance(text);
          speechSynthesis.speak(utterance);
        }
        // block 6 //

        // block 7 //
        function showNextWord(currentWordIndex, words, subjectId) {
  const modal = document.getElementById(`modal_${subjectId}`);
  const modalBody = modal.querySelector('.modal-body');

  // Add logging to track when a new word is displayed
  console.log('Showing next word at index:', currentWordIndex);
  console.log('Words array length:', words.length);
  
  if (currentWordIndex < words.length) {
    const newWord = words[currentWordIndex];
    console.log('New word to display:', newWord.word);
    console.log('New word ID:', newWord.word_id);
  } else {
    console.error('Word index out of bounds:', currentWordIndex);
  }

  modalBody.style.animation = 'slideOutLeft 0.5s forwards';

  setTimeout(() => {
    // Update all word content with correct IDs that include the subject ID
    const wordElement = document.getElementById(`dynamicWord_${subjectId}`);
    wordElement.textContent = words[currentWordIndex].word;
    
    // Store the word ID as a data attribute for direct access
    wordElement.dataset.wordId = words[currentWordIndex].word_id;
    console.log('Word element updated:', wordElement.textContent, 'with ID:', wordElement.dataset.wordId);
    
    document.getElementById(`phoneticTranscription_${subjectId}`).innerHTML =
      words[currentWordIndex].phonetic_spelling +
      ` <span id="speakButton_${subjectId}" class="inline-block ml-2 cursor-pointer speakButton">🔊</span>`;

    // Update example sentence in the specific modal
    const exampleElement = modal.querySelector('.modal-content-bottom .text-lg.italic');
    exampleElement.textContent = words[currentWordIndex].example_sentence;

    modalBody.style.animation = 'slideInRight 0.5s forwards';
  }, 500);
}
        // block 7 //


        // block 8 //
        // Example usage: Update word and phonetic transcription based on section click
        document.querySelectorAll('#mathSection, #engSection').forEach(section => {
          section.addEventListener('click', function () {
            const modalId = section.id === 'mathSection' ? 'mathModal' : 'engModal';
            const modal = document.getElementById(modalId);
            modal.style.display = 'flex';

            // Update content based on the section
            if (section.id === 'mathSection') {
              updateContent('Mathematics');
            } else if (section.id === 'engSection') {
              updateContent('English');
            }
          });
        });
        // block 8 //

        // block 9 //
        document.querySelectorAll('.close-button').forEach(button => {
          button.addEventListener('click', function () {
            const modal = this.closest('.modal');
            const subjectId = modal.id.split('_')[1];

            console.log('Modal closed for subject ID:', subjectId);

            // Hide the modal
            modal.style.display = 'none';

            // Fetch updated progress data
            fetch(`php-functions/get_progress.php?subject_id=${subjectId}`)
              .then(response => {
                if (!response.ok) {
                  throw new Error(`Server returned ${response.status}`);
                }
                return response.json();
              })
              .then(data => {
                console.log('Progress data received:', data);
                if (data.progress !== undefined) {
                  // Update the UI with the new progress
                  updateProgressUI(subjectId, data.progress);
                }
              })
              .catch(error => {
                console.error('Error fetching updated progress:', error);
              });
          });
        });
        // block 9 //

        // block 10 //
        window.addEventListener('click', function (event) {
          if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
          }
        });
        // block 10 //

        // block 11 //
        // Event listener for speaker icon click
        document.addEventListener('DOMContentLoaded', () => {
          document.addEventListener('click', function (event) {
            if (event.target.id.startsWith('speakButton_') || event.target.classList.contains('speakButton')) {
              // Find the closest modal that contains this button
              const modal = event.target.closest('.modal');
              if (modal) {
                // Get the subject ID from the modal ID
                const subjectId = modal.id.split('_')[1];

                // Find the word element within this specific modal using the correct ID
                const wordToSpeak = document.getElementById(`dynamicWord_${subjectId}`).textContent;
                // console.log('Speaking word from modal:', wordToSpeak);
                speakText(wordToSpeak);
              }
            }
          });
        });
        // block 11 //

        // block 12 //	
        // Speech Recognition functionality
        function startRecognition(subjectId) {
          console.log('Starting speech recognition for subject:', subjectId);

          if (!window.SpeechRecognition && !window.webkitSpeechRecognition) {
            // console.error('Speech recognition not supported in this browser');
            alert('Speech recognition is not supported in your browser. Please use Chrome or Edge.');
            return;
          }

          const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
          // Store the recognition instance globally so we can stop it later
          recognitionInstance = recognition;

          const micAnimation = document.getElementById(`micAnimation_${subjectId}`);

          recognition.lang = 'en-US';
          recognition.continuous = false;
          recognition.interimResults = false;

          // Show mic animation when starting
          micAnimation.classList.remove('hidden');
          // console.log('Mic animation shown');

          recognition.onstart = function () {
            console.log('Recognition started');
            isRecording = true;
          };

          recognition.onend = function () {
            // console.log('Recognition ended');
            micAnimation.classList.add('hidden');
            isRecording = false;

            // Reset the mic button
            const micButton = document.getElementById(`micButton_${subjectId}`);
            if (micButton) {
              micButton.textContent = '🔈';
            }
          };

          recognition.onerror = function (event) {
            // console.error('Recognition error:', event.error);
            micAnimation.classList.add('hidden');
            isRecording = false;

            // Reset the mic button
            const micButton = document.getElementById(`micButton_${subjectId}`);
            if (micButton) {
              micButton.textContent = '🔈';
            }

            Swal.fire({
              title: 'Error!',
              text: 'There was a problem with the speech recognition: ' + event.error,
              icon: 'error'
            });
          };

          recognition.onresult = function (event) {
            const transcript = event.results[0][0].transcript.toLowerCase().trim();
            const expectedWord = document.getElementById(`dynamicWord_${subjectId}`).textContent.toLowerCase().trim();
            const confidence = event.results[0][0].confidence;
            const pronunciationScore = evaluatePronunciation(transcript, expectedWord);
            const finalScore = Math.round((pronunciationScore * 0.7 + confidence * 100 * 0.3));

            // console.log('Recognition results:');
            // console.log('- Transcript:', transcript);
            // console.log('- Expected word:', expectedWord);
            // console.log('- Confidence:', confidence);
            // console.log('- Score:', finalScore);

            if (finalScore >= 80) {
              // Debug the subject data access
              console.log('Subject ID:', subjectId);
              console.log('Subject data available:', Object.keys(subjectData));

              // Get the correct subject data
              const currentSubject = subjectData[subjectId] || { words: window.words, currentIndex: window.currentWordIndex };

              // Debug word ID retrieval
              // console.log('Current subject data:', currentSubject);
              // console.log('Words array length:', currentSubject.words ? currentSubject.words.length : 'undefined');
              // console.log('Current index:', currentSubject.currentIndex);

              // Additional check to prevent errors
              if (!currentSubject.words || currentSubject.words.length === 0) {
                // console.error('❌ ERROR: No words data available for this subject!');
                Swal.fire({
                  title: 'Error!',
                  text: 'No word data found. Please try refreshing the page.',
                  icon: 'error'
                });
                return;
              }

              if (currentSubject.currentIndex >= currentSubject.words.length) {
                // console.error('❌ ERROR: Word index out of bounds!');
                Swal.fire({
                  title: 'Error!',
                  text: 'Word index error. Please try refreshing the page.',
                  icon: 'error'
                });
                return;
              }

              // Save progress with the correct word ID
              const wordId = currentSubject.words[currentSubject.currentIndex].word_id;
              // console.log('✅ Saving progress for word ID:', wordId);
              updateProgress(wordId, finalScore);

              // Check if this was the last word
              if (currentSubject.currentIndex === currentSubject.words.length - 1) {
                Swal.fire({
                  title: '🎉 Congratulations!',
                  text: 'You have completed all words in this card!',
                  icon: 'success',
                  confirmButtonText: 'Review Your Work',
                  showConfirmButton: true,
                  allowOutsideClick: false
                }).then((result) => {
                  if (result.isConfirmed) {
                    // Reload the page to show completed words
                    location.reload();
                  }
                });
              } else {
                // Show regular success message and advance
                Swal.fire({
                  title: '🌟 Excellent!',
                  html: `Your score: ${finalScore}%<br>You said: "${transcript}"`,
                  icon: 'success',
                  confirmButtonText: 'Next Word'
                }).then((result) => {
                  if (result.isConfirmed) {
                    // Update both the subject-specific index and the global index
                    currentSubject.currentIndex++;
                    window.currentWordIndex = currentSubject.currentIndex;

                    // Save the updated index back to the subjectData object
                    subjectData[subjectId] = currentSubject;

                    // Show next word
                    showNextWord(currentSubject.currentIndex, currentSubject.words, subjectId);
                  }
                });
              }
            } else {
              // Show retry message for scores below 80%
              Swal.fire({
                title: '🎯 Almost There!',
                html: `Your score: ${finalScore}%<br>You said: "${transcript}"<br>Let's try this word again!`,
                icon: 'info',
                confirmButtonText: 'Try Again'
              });
            }
          };

          recognition.start();
        }
        // block 12 //

        // block 13 //
        function evaluatePronunciation(spokenText, targetWord) {
          // Convert both to lowercase for comparison
          const spoken = spokenText.toLowerCase().trim();
          const target = targetWord.toLowerCase().trim();

          // Initialize scoring variables
          let score = 0;
          const maxScore = 100;

          if (spoken === target) {
            return maxScore;
          }

          const distance = levenshteinDistance(spoken, target);
          const maxLength = Math.max(spoken.length, target.length);

          score = Math.max(0, Math.round((1 - distance / maxLength) * 100));

          return score;
        }
        //block 13 //

        // block 14 //
        function levenshteinDistance(a, b) {
          const matrix = [];

          for (let i = 0; i <= b.length; i++) {
            matrix[i] = [i];
          }

          for (let j = 0; j <= a.length; j++) {
            matrix[0][j] = j;
          }

          for (let i = 1; i <= b.length; i++) {
            for (let j = 1; j <= a.length; j++) {
              if (b.charAt(i - 1) === a.charAt(j - 1)) {
                matrix[i][j] = matrix[i - 1][j - 1];
              } else {
                matrix[i][j] = Math.min(
                  matrix[i - 1][j - 1] + 1,
                  matrix[i][j - 1] + 1,
                  matrix[i - 1][j] + 1
                );
              }
            }
          }

          return matrix[b.length][a.length];
        }
        // block 14 //


        // block 15 //
        const urlParams = new URLSearchParams(window.location.search);
        const currentCardId = urlParams.get('card_id');

        function updateProgress(wordId, score) {
  console.log('Updating progress for word ID:', wordId, 'with score:', score);

  fetch('update_progress.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      word_id: wordId,
      score: score,
      card_id: currentCardId,
      completed_at: new Date().toISOString()
    })
  })
    .then(response => response.json())
    .then(data => {
      console.log('Progress update response:', data);
      
      if (data.success) {
        // Find the subject section and update the progress bar
        if (data.subject_id) {
          updateProgressUI(data.subject_id, data.progress);
          
          // Optionally show success message
          showMessage('Progress updated successfully!');
          
          // If word is completed, you might want to add visual feedback
          if (data.completed) {
            markWordAsCompleted(wordId);
          }
        }
      } else {
        // Handle error case
        console.error('Error in progress update:', data.error);
        showMessage('Failed to update progress: ' + data.error, 'error');
      }
    })
    .catch(error => {
      console.error('Error updating progress:', error);
      showMessage('Failed to connect to server', 'error');
    });
}

        // Helper function to update progress UI
        function updateProgressUI(subjectId, progress) {
          // console.log(`Updating UI for subject ${subjectId} with progress ${progress}%`);

          // Find the subject section by data-subject-id
          const subjectSection = document.querySelector(`[data-subject-id="${subjectId}"]`);

          if (subjectSection) {
            // console.log('Found subject section:', subjectSection);

            // Find the progressbar container
            const progressbarContainer = subjectSection.querySelector('.progressbar');

            if (progressbarContainer) {
              // Find the progress bar
              const progressBar = progressbarContainer.querySelector('.bg-\\[\\#58CC02\\]');
              if (progressBar) {
                // Update width with animation
                const roundedProgress = Math.round(progress);
                progressBar.style.width = `${roundedProgress}%`;
                // console.log(`Updated progress bar to ${roundedProgress}%`);
              } else {
                // console.error('Could not find progress bar element with class .bg-[#58CC02]');
              }

              // Find and update the progress text - specifically targeting the exact structure
              const progressText = progressbarContainer.querySelector('.text-sm.text-gray-600');
              if (progressText) {
                const roundedProgress = Math.round(progress);
                progressText.textContent = `Progress: ${roundedProgress}%`;
                // console.log(`Updated progress text to ${roundedProgress}%`);
              } else {
                console.error('Could not find progress text element with class .text-sm.text-gray-600');
                // Dump all text elements for debugging
                const allTextElements = progressbarContainer.querySelectorAll('*');
                console.log('All elements in progressbar container:', Array.from(allTextElements).map(el => ({
                  tag: el.tagName,
                  classes: el.className,
                  text: el.textContent.trim()
                })));
              }
            } else {
              // console.error('Could not find progressbar container with class .progressbar');
            }
          } else {
            // console.error(`Could not find subject section with data-subject-id="${subjectId}"`);
            // Log all subject sections for debugging
            const allSections = document.querySelectorAll('[data-subject-id]');
            console.log('Available subject sections:', Array.from(allSections).map(el => el.dataset.subjectId));
          }
        }
        // block 15 //

        document.addEventListener('click', function (event) {
          if (event.target.classList.contains('micButton')) {
            const modal = event.target.closest('.modal');
            const subjectId = modal.id.split('_')[1];
            const micAnimation = document.getElementById(`micAnimation_${subjectId}`);

            if (isRecording) {
              if (recognitionInstance) {
                recognitionInstance.stop();
                recognitionInstance = null;
                isRecording = false;
                event.target.textContent = '🔈';
                micAnimation.classList.add('hidden');
              }
            } else {
              startRecognition(subjectId);
            }
          }
        });

        // Handle the speakButtonCompleted and speakButton separately
        document.addEventListener('click', function (event) {
          // Handle completed words speak buttons
          if (event.target.classList.contains('speakButtonCompleted')) {
            // Get the parent list item to find the word text
            const listItem = event.target.closest('li');
            if (listItem) {
              // First span in the list item contains the word text
              const wordElement = listItem.querySelector('span:first-child');
              if (wordElement) {
                const wordToSpeak = wordElement.textContent.trim();
                console.log('Speaking completed word:', wordToSpeak);
                speakText(wordToSpeak);
              }
            }
          }
          // Handle current word speak buttons
          else if (event.target.classList.contains('speakButton')) {
            const modal = event.target.closest('.modal');
            if (modal) {
              const subjectId = modal.id.split('_')[1];
              const wordElement = document.getElementById(`dynamicWord_${subjectId}`);
              if (wordElement) {
                const wordToSpeak = wordElement.textContent.trim();
                console.log('Speaking current word:', wordToSpeak);
                speakText(wordToSpeak);
              }
            }
          }
        });

        // Enhanced speech function
        function speakText(text) {
          if (!text) {
            console.error('Empty text to speak');
            return;
          }

          console.log('Speaking text:', text);

          // Cancel any existing speech
          window.speechSynthesis.cancel();

          const utterance = new SpeechSynthesisUtterance(text);
          utterance.lang = 'en-US';
          utterance.rate = 0.9;  // Slightly slower for clarity

          window.speechSynthesis.speak(utterance);
        }
      </script>
</body>

</html>