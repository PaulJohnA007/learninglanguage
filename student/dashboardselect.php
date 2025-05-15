<?php
session_start();
require_once('../function/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginsignup_page.php");
    exit();
}

// Fetch user data including grade_level
$userSql = "SELECT * FROM users WHERE id = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $_SESSION['user_id']);
$userStmt->execute();
$userData = $userStmt->get_result()->fetch_assoc();

// Store grade_level in session
$_SESSION['grade_level'] = $userData['grade_level'];


// Fetch learning cards from database
$sql = "SELECT * FROM learningcard";
$result = $conn->query($sql);

$previous_completed = true;
// Default progress to 0
$progress = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: '#58CC02',
                        secondary: '#1CB0F6',
                        accent: '#FF4B4B',
                        yellow: '#FFC800',
                        purple: '#A560E8',
                        'accent-hover': '#FF2B2B',
                    },
                    boxShadow: {
                        'sidebar': '0 0 10px rgba(0, 0, 0, 0.1)',
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .grade-card {
            animation: float 4s ease-in-out infinite;
            transition: all 0.3s ease;
        }

        .grade-card:hover {
            transform: scale(1.05) rotate(2deg);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .progress-bar {
            background: linear-gradient(45deg, #FF8C00, #FFA500, #FFD700);
            background-size: 200% 200%;
            animation: gradient 3s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
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

        @keyframes confetti {
            0% {
                transform: translate(0, 0) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translate(var(--tx), var(--ty)) rotate(var(--r));
                opacity: 0;
            }
        }

        .main-content {
            background-image: url('../pics/cartoon-kids-garden.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .main-content::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            z-index: 1;
        }

        .content-wrapper {
            position: relative;
            z-index: 2;
        }

        /* Fixed sidebar layout - add to any page with sidebar scrolling issues */
        .page-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar-container {
            position: fixed;
            width: 280px;
            height: 100vh;
            z-index: 40;
            overflow-y: auto;
            /* Allow sidebar to scroll independently if needed */
        }

        .main-container {
            flex: 1;
            margin-left: 0;
        }

        @media (min-width: 768px) {
            .main-container {
                margin-left: 280px;
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-poppins">
    <div class="page-layout">
        <!-- Sidebar Container -->
        <div class="sidebar-container">
            <?php include 'sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="main-container main-content">
            <div class="content-wrapper p-6 md:p-10">
                <!-- Page Header -->
                <div class="mb-8 mt-10">
                    <h1 class="text-3xl font-bold text-gray-800">Learning Journey</h1>
                    <p class="text-gray-600 mt-2">Explore and master new vocabulary through interactive lessons</p>
                </div>

                <!-- Learning Cards Grid -->
                <div id="cardsContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8" style="display: none;">
                    <!-- Cards will be loaded here via AJAX -->
                </div>


                <!-- Learning Tips Section -->
                <div class="mt-12 bg-white rounded-2xl shadow-lg p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 -mt-10 -mr-10 bg-yellow-400 rounded-full opacity-20">
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Learning Tips</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-blue-50 p-4 rounded-xl">
                                <div class="flex items-center mb-3">
                                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-gray-800">Practice Daily</h3>
                                </div>
                                <p class="text-gray-600 text-sm">Consistent practice for just 10 minutes a day is more
                                    effective than cramming once a week.</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-xl">
                                <div class="flex items-center mb-3">
                                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-gray-800">Use Flashcards</h3>
                                </div>
                                <p class="text-gray-600 text-sm">Flashcards help with memorization through active recall
                                    and spaced repetition.</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-xl">
                                <div class="flex items-center mb-3">
                                    <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-gray-800">Set Reminders</h3>
                                </div>
                                <p class="text-gray-600 text-sm">Schedule learning sessions and set reminders to
                                    maintain your learning streak.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="javascript-handler/cards.js"></script>
    <script>
        document.querySelectorAll('.grade-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                const audio = new Audio('../sounds/pop.mp3');
                audio.volume = 0.2;
                audio.play();
            });
        });

        function confirmLogout(event) {
            event.preventDefault(); // Prevent default link behavior

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to log out?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log out!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php'; // Redirect to logout script
                }
            });
        }

        // Add confetti effect function
        function createConfetti(element) {
            const colors = ['#FFD700', '#FF69B4', '#00CED1', '#98FB98'];

            for (let i = 0; i < 10; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'absolute w-2 h-2 rounded-full pointer-events-none';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.top = Math.random() * 100 + '%';
                confetti.style.setProperty('--tx', (Math.random() * 200 - 100) + 'px');
                confetti.style.setProperty('--ty', (Math.random() * -100 - 50) + 'px');
                confetti.style.setProperty('--r', (Math.random() * 360) + 'deg');
                confetti.style.animation = 'confetti 1s ease-out forwards';

                element.appendChild(confetti);
                setTimeout(() => confetti.remove(), 1000);
            }
        }

        // Add sound effects on hover
        const gradeCards = document.querySelectorAll('.grade-card');
        const hoverSound = new Audio('../sounds/hover.mp3'); // Add a subtle hover sound file

        gradeCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                if (!card.classList.contains('opacity-70')) { // Only play for unlocked cards
                    hoverSound.volume = 0.1;
                    hoverSound.currentTime = 0;
                    hoverSound.play();

                    // Add random confetti effect for completed cards
                    if (card.querySelector('.text-primary')) {
                        createConfetti(card);
                    }
                }
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            // Retrieve username and user_type from query parameters
            const urlParams = new URLSearchParams(window.location.search);
            const username = urlParams.get('username');
            const userType = urlParams.get('user_type');

            // Check if user_type is 'student'
            if (userType === 'student' && username) {
                // Create a text-to-speech message
                const message = `Welcome ${username}`;
                const speech = new SpeechSynthesisUtterance(message);

                // Function to speak the message
                const speakMessage = (voice) => {
                    if (voice) {
                        speech.voice = voice;
                    }
                    window.speechSynthesis.speak(speech);
                };

                // Retrieve available voices and select a female voice
                const onVoicesChanged = () => {
                    const voices = speechSynthesis.getVoices();

                    // Attempt to find a female voice
                    const femaleVoice = voices.find(voice => voice.name.toLowerCase().includes('female') || voice.name.toLowerCase().includes('woman'));

                    // Speak the message with the selected voice
                    speakMessage(femaleVoice);
                };

                // Check if voices are already loaded
                if (speechSynthesis.getVoices().length > 0) {
                    onVoicesChanged();
                } else {
                    // Wait for voices to be loaded
                    speechSynthesis.onvoiceschanged = onVoicesChanged;
                }
            }
        });
    </script>
</body>

</html>