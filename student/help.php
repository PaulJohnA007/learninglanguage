<?php
session_start();
require_once('../function/dbconnect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginsignup_page.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - English Learning Platform</title>
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

        .help-card {
            transition: all 0.3s ease;
        }

        .help-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .faq-item {
            transition: all 0.3s ease;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
        }

        .faq-item.active .faq-toggle {
            transform: rotate(180deg);
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

        /* Custom styles for this page only */
        .page-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar-container {
            position: fixed;
            width: 280px;
            height: 100vh;
            z-index: 40;
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
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">Help Center</h1>
                    <p class="text-gray-600 mt-2">Find answers to common questions and get support</p>
                </div>

                <!-- Help Categories -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <!-- Getting Started -->
                    <div class="help-card bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Getting Started</h3>
                            <p class="text-gray-600 text-sm mb-4">Learn the basics of using our platform and start your
                                learning journey.</p>
                            <a href="#getting-started" class="text-primary font-medium text-sm flex items-center">
                                Learn more
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Learning Features -->
                    <div class="help-card bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-secondary" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Learning Features</h3>
                            <p class="text-gray-600 text-sm mb-4">Discover all the learning tools and features available
                                to enhance your experience.</p>
                            <a href="#learning-features" class="text-secondary font-medium text-sm flex items-center">
                                Learn more
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Technical Support -->
                    <div class="help-card bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="w-12 h-12 bg-purple/10 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Technical Support</h3>
                            <p class="text-gray-600 text-sm mb-4">Get help with technical issues, account problems, or
                                contact our support team.</p>
                            <a href="#technical-support" class="text-purple font-medium text-sm flex items-center">
                                Learn more
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Frequently Asked Questions -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Frequently Asked Questions</h2>

                    <div class="space-y-4">
                        <!-- FAQ Item 1 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="w-full flex justify-between items-center p-4 text-left focus:outline-none">
                                <span class="font-medium text-gray-800">How do I track my learning progress?</span>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="faq-toggle h-5 w-5 text-gray-500 transition-transform duration-300"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="faq-answer px-4 pb-4">
                                <p class="text-gray-600">
                                    Your learning progress is tracked automatically as you complete lessons and practice
                                    exercises. You can view your progress on your profile page, which shows your
                                    completed words, streak days, and overall progress percentage. The sidebar also
                                    displays your current streak and daily XP earned.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="w-full flex justify-between items-center p-4 text-left focus:outline-none">
                                <span class="font-medium text-gray-800">How do I reset my password?</span>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="faq-toggle h-5 w-5 text-gray-500 transition-transform duration-300"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="faq-answer px-4 pb-4">
                                <p class="text-gray-600">
                                    To reset your password, go to the login page and click on "Lost your magic spell?"
                                    link. Enter your email address, and we'll send you a password reset link. Follow the
                                    instructions in the email to create a new password.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="w-full flex justify-between items-center p-4 text-left focus:outline-none">
                                <span class="font-medium text-gray-800">How does the streak system work?</span>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="faq-toggle h-5 w-5 text-gray-500 transition-transform duration-300"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="faq-answer px-4 pb-4">
                                <p class="text-gray-600">
                                    Streaks track your consistent learning activity. You earn a streak day by completing
                                    at least one learning activity each day. If you miss a day, your streak will reset
                                    to 1 the next time you complete an activity. Maintaining a streak helps build a
                                    consistent learning habit and can earn you bonus rewards.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 4 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="w-full flex justify-between items-center p-4 text-left focus:outline-none">
                                <span class="font-medium text-gray-800">Why are some learning cards locked?</span>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="faq-toggle h-5 w-5 text-gray-500 transition-transform duration-300"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="faq-answer px-4 pb-4">
                                <p class="text-gray-600">
                                    Learning cards follow a progressive learning path. You need to complete the previous
                                    card with 100% progress before unlocking the next one. This ensures you build a
                                    strong foundation before moving to more advanced content. Complete all words in a
                                    card to unlock the next level.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 5 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="w-full flex justify-between items-center p-4 text-left focus:outline-none">
                                <span class="font-medium text-gray-800">How is my pronunciation scored?</span>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="faq-toggle h-5 w-5 text-gray-500 transition-transform duration-300"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="faq-answer px-4 pb-4">
                                <p class="text-gray-600">
                                    Our system uses speech recognition technology to analyze your pronunciation. When
                                    you speak a word, the system compares your pronunciation to the correct one and
                                    provides a score. Practice regularly to improve your pronunciation score. Remember
                                    to speak clearly and at a normal pace for the best results.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Topics Sections -->
                <div id="getting-started" class="bg-white rounded-xl shadow-md p-6 mb-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Getting Started</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Creating Your Account</h3>
                            <p class="text-gray-600">
                                To get started, create an account by clicking the "Join Us!" button on the login page.
                                Fill in your username, email, and password. You can also upload a profile picture to
                                personalize your account.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Navigating the Dashboard</h3>
                            <p class="text-gray-600">
                                After logging in, you'll see your learning dashboard with available learning cards. Each
                                card represents a different level and category of learning content. Start with the first
                                unlocked card and progress through them as you complete each level.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Understanding Your Progress</h3>
                            <p class="text-gray-600">
                                Your progress is displayed in several places:
                            </p>
                            <ul class="list-disc list-inside text-gray-600 ml-4 mt-2 space-y-1">
                                <li>The sidebar shows your current streak and daily XP</li>
                                <li>Each learning card displays your completion percentage</li>
                                <li>Your profile page shows detailed statistics about your learning journey</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="learning-features" class="bg-white rounded-xl shadow-md p-6 mb-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Learning Features</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Learning Cards</h3>
                            <p class="text-gray-600">
                                Learning cards are organized by difficulty level and category. Each card contains a
                                collection of words and subjects to learn. Complete all words in a card to unlock the
                                next level.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Pronunciation Practice</h3>
                            <p class="text-gray-600">
                                Practice your pronunciation by clicking the "Listen" button to hear the correct
                                pronunciation, then use the microphone button to record your own pronunciation. The
                                system will score your pronunciation accuracy.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Subject Categories</h3>
                            <p class="text-gray-600">
                                Words are organized into subject categories like English, Mathematics, Science, and
                                History. This helps you learn vocabulary relevant to different academic subjects and
                                contexts.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Streaks and Rewards</h3>
                            <p class="text-gray-600">
                                Maintain a daily learning streak to build consistency. Each day you complete at least
                                one learning activity, your streak increases. Longer streaks may unlock special rewards
                                and achievements.
                            </p>
                        </div>
                    </div>
                </div>

                <div id="technical-support" class="bg-white rounded-xl shadow-md p-6 mb-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Technical Support</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Browser Requirements</h3>
                            <p class="text-gray-600">
                                For the best experience, use the latest version of Chrome, Firefox, Safari, or Edge.
                                Enable JavaScript and cookies in your browser settings. For pronunciation features,
                                allow microphone access when prompted.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Account Recovery</h3>
                            <p class="text-gray-600">
                                If you forget your password, use the "Lost your magic spell?" link on the login page to
                                reset it. If you can't access your email, contact support with your username and any
                                information that can verify your identity.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Common Issues</h3>
                            <p class="text-gray-600">
                                Here are solutions to common technical problems:
                            </p>
                            <ul class="list-disc list-inside text-gray-600 ml-4 mt-2 space-y-1">
                                <li>Microphone not working: Check browser permissions and device settings</li>
                                <li>Progress not saving: Ensure you're connected to the internet and not in private
                                    browsing mode</li>
                                <li>Page loading slowly: Try clearing your browser cache or using a different browser
                                </li>
                                <li>Login issues: Clear cookies or try resetting your password</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // FAQ accordion functionality
            const faqItems = document.querySelectorAll('.faq-item button');

            faqItems.forEach(item => {
                item.addEventListener('click', () => {
                    const parent = item.parentElement;
                    parent.classList.toggle('active');
                });
            });

            // Smooth scroll to sections
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);

                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 20,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Support form submission
            const supportForm = document.getElementById('supportForm');

            supportForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const subject = document.getElementById('subject').value;
                const message = document.getElementById('message').value;

                if (!subject || !message) {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Please fill in all fields',
                        icon: 'error',
                        confirmButtonColor: '#58CC02',
                        background: '#FFF9F0'
                    });
                    return;
                }

                // Here you would typically send the form data to a server endpoint
                // For demonstration, we'll just show a success message

                Swal.fire({
                    title: 'Message Sent!',
                    text: 'Our support team will get back to you soon.',
                    icon: 'success',
                    confirmButtonColor: '#58CC02',
                    background: '#FFF9F0'
                }).then(() => {
                    // Reset form after successful submission
                    supportForm.reset();
                });
            });

            // Add hover sound effects for interactive elements
            const interactiveElements = document.querySelectorAll('.help-card, .faq-item button');

            interactiveElements.forEach(element => {
                element.addEventListener('mouseenter', () => {
                    // You can add a subtle hover sound here if desired
                    // const hoverSound = new Audio('../sounds/hover.mp3');
                    // hoverSound.volume = 0.1;
                    // hoverSound.play();
                });
            });
        });
    </script>
</body>

</html>