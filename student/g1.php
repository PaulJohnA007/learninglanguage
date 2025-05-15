<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
  <style>
    /* For animation */
    .menu-toggle {
      transition: max-height 0.3s ease-in-out;
    }

    .fixed-img {
      width: 500px;
      height: 250px;
      object-fit: cover;
    }

    /* Modal styles */
    .modal {
      display: none;
      /* Hidden by default */
      position: fixed;
      /* Stay in place */
      z-index: 50;
      /* Sit on top */
      left: 0;
      top: 0;
      width: 100%;
      /* Full width */
      height: 100%;
      /* Full height */
      overflow: auto;
      /* Enable scroll if needed */
      background-color: rgba(0, 0, 0, 0.4);
      /* Black background with opacity */
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border-radius: 20px;
      border: 1px solid #888;
      width: 1000px;
      height: 600px;
      /* Maintain fixed height */
      max-height: 90vh;
      /* Max height with viewport */
      overflow-y: auto;
      /* Scroll if content is too long */
      position: relative;
      display: flex;
      flex-direction: column;
    }

    .modal-header {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }

    .modal-title {
      flex: 1;
      text-align: left;
      font-size: 1.25rem;
      font-weight: bold;
    }

    .modal-body {
      display: flex;
      flex-direction: column;
      justify-content: center;
      /* Center the content vertically */
      height: calc(100% - 60px);
      /* Adjust to fit within 600px height, accounting for header and padding */
    }

    .modal-content-center {
      text-align: center;
      margin-top: 100px;
      /* Add space below the centered content */
    }

    .modal-content-left {
      text-align: left;
      margin-top: 20px;
      /* Add space above the left-aligned content */
      margin-left: 0;
      /* Ensure alignment starts from the left */
    }

    .modal-content-bottom {
      text-align: center;
      margin-top: auto;
      /* Push this content to the bottom */
    }

    .close-button {
      position: absolute;
      top: 15px;
      right: 15px;
      font-size: 1.5rem;
      cursor: pointer;
    }

    /* Microphone animation styles */
    .mic-animation {
      text-align: center;
      margin: 20px 0;
    }

    .mic-pulse {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: rgba(76, 175, 80, 0.3);
      position: relative;
      margin: 0 auto;
      animation: pulse 1.5s ease-in-out infinite;
    }

    .mic-icon {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 40px;
      animation: bounce 1s infinite;
    }

    .mic-status {
      margin-top: 10px;
      font-size: 18px;
      color: #4CAF50;
      font-weight: bold;
    }

    @keyframes pulse {
      0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
      }

      70% {
        transform: scale(1);
        box-shadow: 0 0 0 30px rgba(76, 175, 80, 0);
      }

      100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
      }
    }
  </style>
</head>

<body>
  <div class="min-h-screen flex flex-col md:flex-row relative">
    <!-- Burger Menu Icon -->
    <div class="md:hidden flex justify-between p-1 text-white bg-[#0038A8]">
      <span class="text-2xl text-center font-bold">DASHBOARD</span>
      <button id="burgerMenu" class="bg-[#0038A8] text-3xl focus:outline-none">
        &#9776;
      </button>
    </div>

    <!-- Sidebar -->
    <aside
      class="menu-toggle w-full md:w-64 text-white flex flex-col bg-white md:static absolute max-h-0 md:max-h-full overflow-hidden z-40"
      id="sidebar">
      <div class="p-4 text-center text-2xl font-bold text-[#0038A8] hidden md:block">DASHBOARD</div>
      <nav class="flex-1 p-4 space-y-4">
        <a href="dashboardselect.php"
          class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105"
          data-modal="mathModal">Home</a>
        <a href="profilepage.php"
          class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Profile</a>
        <a href="#"
          class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Settings</a>

        <!-- Grade Selection Burger Menu -->
        <div class="p-4 mt-auto">
          <button id="gradeBurgerMenu"
            class="w-full h-10 bg-[#0038A8] text-3xl text-white flex justify-center items-center p-2 rounded focus:outline-none">
            &#9776;
          </button>

          <div id="gradeMenu" class="mt-1 hidden flex-col space-y-2">
            <a href="g1.html"
              class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade
              1</a>
            <a href="g2.html"
              class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade
              2</a>
            <a href="g3.html"
              class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade
              3</a>
            <a href="g4.html"
              class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade
              4</a>
            <a href="g5.html"
              class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade
              5</a>
            <a href="g6.html"
              class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade
              6</a>
          </div>
        </div>
      </nav>
    </aside>

    <!-- Main Content -->
    <div
      class="min-h-screen bg-gradient-to-r from-purple-400 to-blue-500 flex flex-col items-center justify-center p-4 space-y-6 flex-1 bg-cover bg-center"
      style="background-image: url('../pics/back1.jpg');">
      <div
        class="bg-yellow-500 rounded-3xl shadow-2xl p-2 w-full max-w-sm text-center transform transition-transform duration-500 hover:scale-105  animate-bounce">
        <h1 class="text-2xl font-bold text-primary">WELCOME TO GRADE 1!</h1>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full max-w-3xl">
        <div id="mathSection"
          class="cursor-pointer bg-gradient-to-r from-green-200 to-green-400 rounded-2xl p-4 flex flex-col items-center transform transition-transform duration-500 hover:rotate-6 hover:scale-105">
          <img class="rounded-2xl fixed-img" src="../pics/g1p.jpg" alt="math" class="mb-4">
          <h3 class="text-lg font-semibold text-primary-foreground">Mathematics</h3>
          <p class="text-sm text-muted-foreground">Reading some Words</p>
        </div>
        <div id="engSection"
          class="cursor-pointer bg-gradient-to-r from-orange-200 to-orange-400 rounded-2xl p-4 flex flex-col items-center transform transition-transform duration-500 hover:rotate-6 hover:scale-105">
          <img class="rounded-2xl fixed-img" src="../pics/g1pp.jpg" alt="eng" class="mb-4">
          <h3 class="text-lg font-semibold text-accent-foreground">English</h3>
          <p class="text-sm text-muted-foreground">Reading some Words</p>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal for Mathematics -->
  <div id="mathModal" class="modal">
    <div class="modal-content">
      <span class="close-button">×</span>
      <div class="modal-header">
        <h2 class="modal-title">MATHEMATICS SECTION</h2>
      </div>
      <div class="modal-body">
        <!-- Centered Content -->
        <div class="modal-content-center">
          <h2 class="text-xl font-bold mb-2">SAY THE FOLLOWING WORD:</h2>
          <div class="text-lg font-bold" style="font-size: 35px;" id="dynamicWord">Mathematics</div>
        </div>
        <!-- Left Aligned Content -->
        <div class="modal-content-left">
          <div class="text-lg font-semibold">SOUNDS LIKE:</div>
          <div class="text-lg" id="phoneticTranscription">maTH(ə)ˈmadiks/ <span id="speakButton"
              class="inline-block ml-2">🔊</span></div>
        </div>
        <!-- Bottom Centered Content -->
        <div class="modal-content-bottom">
          <div class="text-lg font-semibold">Tap to Speak:</div>
          <div class="text-lg">Click <span id="micButton" class="inline-block ml-2">🔈</span> to speak</div>
        </div>
        <!-- Add this to the modal content -->
        <div class="mic-animation hidden" id="micAnimation">
          <div class="mic-pulse"></div>
          <div class="mic-status">Listening...</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for English -->
  <div id="engModal" class="modal">
    <div class="modal-content">
      <span class="close-button">×</span>
      <div class="modal-header">
        <h2 class="modal-title">ENGLISH SECTION</h2>
      </div>
      <div class="modal-body">
        <!-- Centered Content -->
        <div class="modal-content-center">
          <h2 class="text-xl font-bold mb-2">SAY THE FOLLOWING WORD:</h2>
          <div class="text-lg font-bold" style="font-size: 35px;">English</div>
        </div>
        <!-- Left Aligned Content -->
        <div class="modal-content-left">
          <div class="text-lg font-semibold">SOUNDS LIKE:</div>
          <div class="text-lg">ing-glish <span id="speakButton" class="inline-block ml-2">🔊</span></div>
        </div>
        <!-- Bottom Centered Content -->
        <div class="modal-content-bottom">
          <div class="text-lg font-semibold">Tap to Speak:</div>
          <div class="text-lg">Click <span id="micButton" class="inline-block ml-2">🔈</span> to speak</div>
        </div>
        <!-- Add this to the modal content -->
        <div class="mic-animation hidden" id="micAnimation">
          <div class="mic-pulse"></div>
          <div class="mic-status">Listening...</div>
        </div>
      </div>
    </div>
  </div>


  <script>

  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.getElementById('burgerMenu').addEventListener('click', function () {
      const sidebar = document.getElementById('sidebar');
      if (sidebar.style.maxHeight) {
        sidebar.style.maxHeight = null;
      } else {
        sidebar.style.maxHeight = sidebar.scrollHeight + 'px';
      }
    });

    document.getElementById('gradeBurgerMenu').addEventListener('click', function () {
      const gradeMenu = document.getElementById('gradeMenu');
      if (gradeMenu.style.display === 'flex') {
        gradeMenu.style.display = 'none';
      } else {
        gradeMenu.style.display = 'flex';
      }
    });

    document.querySelectorAll('.menu-item').forEach(item => {
      item.addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        sidebar.style.maxHeight = null;
      });
    });

    // Mapping of words to phonetic transcriptions
    const phoneticMap = {
      'Mathematics': 'maTH(ə)ˈmadiks/',
      'English': '/ˈɪŋɡlɪʃ/'
      // Add more mappings as needed
    };

    // Function to update the word and phonetic transcription
    function updateContent(newWord) {
      // Update the word
      document.getElementById('dynamicWord').textContent = newWord;

      // Update the phonetic transcription based on the new word
      const phoneticTranscription = phoneticMap[newWord] || 'Phonetic transcription not found';
      document.getElementById('phoneticTranscription').innerHTML = phoneticTranscription +
        ' <span id="speakButton" class="inline-block ml-2">&#128266;</span>';
    }

    // Function to speak the text
    function speakText(text) {
      const utterance = new SpeechSynthesisUtterance(text);
      speechSynthesis.speak(utterance);
    }

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

    document.querySelectorAll('.close-button').forEach(button => {
      button.addEventListener('click', function () {
        const modal = this.closest('.modal');
        modal.style.display = 'none';
      });
    });

    window.addEventListener('click', function (event) {
      if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
      }
    });

    // Event listener for speaker icon click
    document.addEventListener('DOMContentLoaded', () => {
      document.addEventListener('click', function (event) {
        if (event.target.id === 'speakButton') {
          // Find the word to speak
          const wordToSpeak = document.getElementById('dynamicWord').textContent;
          speakText(wordToSpeak);
        }
      });
    });
    // Speech Recognition functionality
    function startRecognition() {
      const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
      const micAnimation = document.getElementById('micAnimation');

      recognition.lang = 'en-US';
      recognition.continuous = false;
      recognition.interimResults = false;

      // Show mic animation when starting
      micAnimation.classList.remove('hidden');

      recognition.onend = function () {
        // Hide mic animation when done
        micAnimation.classList.add('hidden');
      };

      recognition.onresult = function (event) {
        const transcript = event.results[0][0].transcript;
        const expectedWord = document.getElementById('dynamicWord').textContent;
        const confidence = event.results[0][0].confidence;

        // Get pronunciation score
        const pronunciationScore = evaluatePronunciation(transcript, expectedWord);
        const finalScore = Math.round((pronunciationScore * 0.7 + confidence * 100 * 0.3));

        // Hide mic animation before showing result
        micAnimation.classList.add('hidden');

        Swal.fire({
          title: finalScore >= 70 ? '🌟 Great Job!' : '🎯 Keep Trying!',
          html: `Your score: ${finalScore}%<br>You said: "${transcript}"`,
          icon: finalScore >= 70 ? 'success' : 'info',
          confirmButtonText: 'Try Again',
          background: '#fff9e6',
          confirmButtonColor: '#4CAF50'
        });
      };

      recognition.onerror = function (event) {
        Swal.fire({
          title: 'Oops!',
          text: 'Please check your microphone and try again.',
          icon: 'error'
        });
      };

      recognition.start();
    }

    function evaluatePronunciation(spokenText, targetWord) {
      // Convert both to lowercase for comparison
      const spoken = spokenText.toLowerCase().trim();
      const target = targetWord.toLowerCase().trim();

      // Initialize scoring variables
      let score = 0;
      const maxScore = 100;

      // Perfect match
      if (spoken === target) {
        return maxScore;
      }

      // Calculate Levenshtein distance
      const distance = levenshteinDistance(spoken, target);
      const maxLength = Math.max(spoken.length, target.length);

      // Calculate similarity score
      score = Math.max(0, Math.round((1 - distance / maxLength) * 100));

      return score;
    }

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
    // Event listener for microphone icon click
    document.addEventListener('click', function (event) {
      if (event.target.id === 'micButton') {
        startRecognition();
      }
    });
  </script>

</body>

</html>