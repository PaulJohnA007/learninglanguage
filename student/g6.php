<?php
session_start();
include '../config.php'; // Database connection
if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginsignup_page.php"); // Redirect to login page if not logged in
    exit();
}
?>

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
      width: 350px;
      height: 250px;
      object-fit: cover;
    }  
    .active {
    background-color: gray;
    cursor: not-allowed;
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
    <aside class="menu-toggle w-full md:w-64 text-white flex flex-col bg-white md:static absolute max-h-0 md:max-h-full overflow-hidden z-40" id="sidebar">
      <div class="p-4 text-center text-2xl font-bold text-[#0038A8] hidden md:block">DASHBOARD</div>
        <nav class="flex-1 p-4 space-y-4">
            <a href="dashboardselect.php" class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Home</a>
            <a href="profilepage.html" class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Profile</a>
            <a href="#" class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Settings</a>
      
            <!-- Grade Selection Burger Menu -->
            <div class="p-4 mt-auto">
                <button id="gradeBurgerMenu" class="w-full h-10 bg-[#0038A8] text-3xl text-white flex justify-center items-center p-2 rounded focus:outline-none">
                    &#9776;
                </button>
                
                
                <div id="gradeMenu" class="mt-1 hidden flex-col space-y-2">
                  <a href="g1.html" class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade 1</a>
                  <a href="g2.html" class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade 2</a>
                  <a href="g3.html" class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade 3</a>
                  <a href="g4.html" class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade 4</a>
                  <a href="g5.html" class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade 5</a>
                  <a href="g6.html" class="text-[#282828] block p-2 rounded bg-[#C5EBFE] hover:bg-[#0165FC] transition-ease menu-item focus:outline-none transition-all duration-300 transform hover:scale-105">Grade 6</a>
                </div>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="min-h-screen bg-gradient-to-r from-purple-400 to-blue-500 flex flex-col items-center justify-center p-4 space-y-6 flex-1 bg-cover bg-center" style="background-image: url('/pics/backg6.jpg');">
      <div class="bg-yellow-500 rounded-3xl shadow-2xl p-2 w-full max-w-sm text-center transform transition-transform duration-500 hover:scale-105  animate-bounce">
        <!-- <h2 class="text-lg font-semibold text-foreground">Good Morning,</h2>
        <h1 class="text-2xl pb-4 font-bold text-blue-700">Adonis G!</h1> -->
        <h1 class="text-2xl font-bold text-primary">WELCOME TO GRADE 6!</h1>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full max-w-3xl">
        <div class="cursor-pointer bg-gradient-to-r from-green-200 to-green-400 rounded-2xl p-4 flex flex-col items-center transform transition-transform duration-500 hover:rotate-6 hover:scale-105">
          <img class="rounded-2xl fixed-img" src="../pics/backg6z.jpg" alt="math" class="mb-4">
          <h3 class="text-lg font-semibold text-primary-foreground">Mathematics</h3>
          <p class="text-sm text-muted-foreground">Reading some Words</p>
          <button class="complete-quiz-btn bg-blue-500 text-white px-6 py-3 rounded-lg text-lg mt-4" data-subject="Mathematics">
  Complete Quiz
</button>

        </div>

        <div class="cursor-pointer bg-gradient-to-r from-orange-200 to-orange-400 rounded-2xl p-4 flex flex-col items-center transform transition-transform duration-500 hover:rotate-6 hover:scale-105">
          <img class="rounded-2xl fixed-img" src="../pics/backg6zz.jpg" alt="eng" class="mb-4">
          <h3 class="text-lg font-semibold text-accent-foreground">English</h3>
          <p class="text-sm text-muted-foreground">Reading some Words</p>
          <button class="complete-quiz-btn bg-blue-500 text-white px-6 py-3 rounded-lg text-lg mt-4" data-subject="English">
  Complete Quiz
</button>
        </div>

        <div class="cursor-pointer bg-gradient-to-r from-purple-200 to-blue-400 rounded-2xl p-4 flex flex-col items-center transform transition-transform duration-500 hover:rotate-6 hover:scale-105">
          <img class="rounded-2xl fixed-img" src="../pics/backg6zzz.jpg" alt="scien" class="mb-4">
          <h3 class="text-lg font-semibold text-secondary-foreground">Science</h3>
          <p class="text-sm text-muted-foreground">Reading some Words</p>
          <button class="complete-quiz-btn bg-blue-500 text-white px-6 py-3 rounded-lg text-lg mt-4" data-subject="Science">
  Complete Quiz
</button>
        </div>

        <div class="cursor-pointer bg-gradient-to-r from-purple-200 to-purple-400 rounded-2xl p-4 flex flex-col items-center transform transition-transform duration-500 hover:rotate-6 hover:scale-105">
          <img class="rounded-2xl fixed-img" src="../pics/backg6zzzz.jpg" alt="music" class="mb-4">
          <h3 class="text-lg font-semibold text-destructive-foreground">Music</h3>
          <p class="text-sm text-muted-foreground">Reading some Words</p>
          <button class="complete-quiz-btn bg-blue-500 text-white px-6 py-3 rounded-lg text-lg mt-4" data-subject="Music">
  Complete Quiz
</button>
        </div>
      </div>
    </div>
  </div>
  <div id="modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-8 w-[30rem]"> <!-- Increased width -->
      <h2 id="subjectTitle" class="text-2xl font-bold mb-6 text-center"> <!-- Larger font size -->
        Subject Title
      </h2>
      <p id="sentenceText" class="text-lg mb-6 text-center"> <!-- Larger text -->
        Say the following sentence:
      </p>
      <div class="flex items-center justify-center mb-6">
        <button id="playSentence" class="bg-blue-500 text-white px-6 py-3 rounded-lg text-lg"> <!-- Larger button -->
          🔊 Play Sentence
        </button>
        <span id="volumeHint" class="ml-6 text-gray-600 text-sm"> <!-- Larger margin -->
          Click to hear it
        </span>
      </div>
      <button id="startSpeaking" class="bg-green-500 text-white px-6 py-3 rounded-lg text-lg block w-full mb-4"> <!-- Full-width and larger button -->
        🎙️ Start Speaking
      </button>
      <p id="feedback" class="text-red-500 mt-4 hidden text-center text-lg"> <!-- Centered feedback -->
        Incorrect speech. Please try again.
      </p>
      <button id="closeModal" class="mt-6 bg-red-500 text-white px-6 py-3 rounded-lg text-lg block w-full"> <!-- Full-width button -->
        Close
      </button>
    </div>
  </div>
  
  <script>
document.querySelectorAll('.complete-quiz-btn').forEach(button => {
  button.addEventListener('click', function() {
    const subject = button.getAttribute('data-subject');
    const progress = 25; // Assuming the quiz is completed, set the progress to 100%

    // Send the progress to the server
    fetch('save_progress.php', {
      method: 'POST',
      body: JSON.stringify({ subject: subject, progress: progress }),
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(response => response.text())  // Get the raw response as text
    .then(data => {
      console.log('Raw response:', data);  // Log the raw response to check what is returned
      try {
        const jsonData = JSON.parse(data);  // Try parsing it as JSON
        console.log('Parsed JSON:', jsonData);
        if (jsonData.status === 'success') {
          alert('Quiz progress saved successfully!');
        } else {
          alert('Error saving progress: ' + (jsonData.message || 'Unknown error'));
        }
      } catch (e) {
        console.error('Error parsing JSON:', e);
        alert('Error: Invalid response from server');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error saving progress');
    });
  });
});




    document.getElementById('burgerMenu').addEventListener('click', function() {
      const sidebar = document.getElementById('sidebar');
      if (sidebar.style.maxHeight) {
        sidebar.style.maxHeight = null;
      } else {
        sidebar.style.maxHeight = sidebar.scrollHeight + 'px';
      }
    });

    document.getElementById('gradeBurgerMenu').addEventListener('click', function() {
      const gradeMenu = document.getElementById('gradeMenu');
      if (gradeMenu.style.display === 'flex') {
        gradeMenu.style.display = 'none';
      } else {
        gradeMenu.style.display = 'flex';
      }
    });

    document.querySelectorAll('.menu-item').forEach(item => {
      item.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.style.maxHeight = null;
      });
    });
    

    const sentences = {
    Mathematics: [
      "Mathematics is the study of numbers.",
      "Geometry is a part of mathematics.",
      "Algebra is very useful in solving equations.",
      "The Pythagorean theorem is about triangles.",
      "Math is fun and challenging."
    ],
    English: [
      "I enjoy reading books in English.",
      "English is an international language.",
      "Grammar is important in English.",
      "Vocabulary helps us express ourselves.",
      "Writing in English is an art."
    ],
    Science: [
      "Science helps us understand the world.",
      "Biology is the study of life.",
      "Physics explains how things move.",
      "Chemistry studies elements and compounds.",
      "The scientific method involves experiments."
    ],
    Music: [
      "Music soothes the soul.",
      "Learning to play an instrument is fun.",
      "Beethoven is a famous composer.",
      "Rhythm is an important part of music.",
      "Singing improves your mood."
    ]
  };

  const modal = document.getElementById('modal');
  const subjectTitle = document.getElementById('subjectTitle');
  const sentenceText = document.getElementById('sentenceText');
  const playSentence = document.getElementById('playSentence');
  const startSpeaking = document.getElementById('startSpeaking');
  const feedback = document.getElementById('feedback');
  const closeModal = document.getElementById('closeModal');
  let currentSubject = '';
  let currentSentenceIndex = 0;

  // Function to open modal
  function openModal(subject) {
    currentSubject = subject;
    currentSentenceIndex = 0;
    subjectTitle.textContent = subject;
    updateSentence();
    modal.classList.remove('hidden');
  }

  // Function to close modal
  closeModal.addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  // Function to update the sentence in modal
  function updateSentence() {
    if (currentSentenceIndex < sentences[currentSubject].length) {
      sentenceText.textContent = sentences[currentSubject][currentSentenceIndex];
    } else {
      alert("You've completed all sentences for " + currentSubject + "!");
      modal.classList.add('hidden');
    }
  }

  // Text-to-Speech
  playSentence.addEventListener('click', () => {
  const utterance = new SpeechSynthesisUtterance(sentences[currentSubject][currentSentenceIndex]);
  const voices = speechSynthesis.getVoices();
  const filipinoVoice = voices.find(voice => voice.lang === 'fil-PH');
  utterance.voice = filipinoVoice || voices[0]; 
  // Optionally, adjust the speed and pitch for a more natural accent
  utterance.rate = 0.7;  // Speed of speech (1 is normal speed)
  utterance.pitch = 0.5; // Pitch of the voice (1 is default, adjust as needed)
  speechSynthesis.speak(utterance);
});

  // Speech Recognition
  startSpeaking.addEventListener('click', () => {
  // Add Tailwind classes to indicate the button is active
  startSpeaking.classList.add('bg-gray-500', 'cursor-not-allowed');
  startSpeaking.classList.remove('bg-green-500'); // Remove the original green color

  const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
  recognition.lang = 'en-US';
  recognition.start();

  recognition.onresult = (event) => {
    const spokenText = event.results[0][0].transcript.toLowerCase().trim();
    const expectedText = sentences[currentSubject][currentSentenceIndex]
      .toLowerCase()
      .trim()
      .replace(/[^a-zA-Z0-9\s]/g, ''); // Remove punctuation

    if (spokenText === expectedText) {
      feedback.textContent = "Correct! Great job!";
      feedback.classList.remove('hidden', 'text-red-500');
      feedback.classList.add('text-green-500');
      setTimeout(() => feedback.classList.add('hidden'), 3000);

      currentSentenceIndex++;
      updateSentence();
    } else {
      feedback.textContent = `Incorrect speech. You said: "${spokenText}". Try again!`;
      feedback.classList.remove('hidden', 'text-green-500');
      feedback.classList.add('text-red-500');
      setTimeout(() => feedback.classList.add('hidden'), 3000);
    }
  };

  recognition.onerror = (event) => {
    alert('Speech recognition error: ' + event.error);
  };

  recognition.onend = () => {
    // Revert the button back to its original state
    startSpeaking.classList.remove('bg-gray-500', 'cursor-not-allowed');
    startSpeaking.classList.add('bg-green-500');
  };
});

  // Add click listeners to cards
  document.querySelectorAll('.grid > div').forEach((card) => {
    card.addEventListener('click', () => {
      const subject = card.querySelector('h3').textContent;
      openModal(subject);
    });
  });
  </script>
</body>
</html>
