<?php
session_start();
require_once('../function/dbconnect.php');

// Basic authentication check
if (!isset($_SESSION['user_id'])) {
  header("Location: ../loginsignup_page.php");
  exit();
}

// Get the card_id from URL parameter
$card_id = isset($_GET['card_id']) ? (int) $_GET['card_id'] : 0;

// Fetch the learning card details
$cardSql = "SELECT * FROM learningcard WHERE card_id = ?";
$cardStmt = $conn->prepare($cardSql);
$cardStmt->bind_param("i", $card_id);
$cardStmt->execute();
$cardResult = $cardStmt->get_result();
$learningCard = $cardResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="../assets/css/learn.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#58CC02',
            secondary: '#1CB0F6',
            accent: '#FF4B4B',
            yellow: '#FFC800',
            purple: '#A560E8',
          },
          screens: {
            'xs': '360px',
            'sm': '560px', 
          }
        }
      }
    }
</script>
</head>

<style>
  * {
    font-family: 'Poppins', sans-serif;
  }

  .fixed-img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px 8px 0 0;
  }

  .progressbar {
    width: 100%;
  }

  /* Add a glow effect to the progress bar when it's filling */
  .bg-blue-600 {
    box-shadow: 0 0 8px rgba(37, 99, 235, 0.5);
    background: linear-gradient(90deg, #4f46e5, #3b82f6);
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

  /* For any other elements that might have scrollbars */
  .no-scrollbar {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }

  .no-scrollbar::-webkit-scrollbar {
    display: none;
  }
  .hide-scrollbar {
    scrollbar-width: none; /* For Firefox */
    -ms-overflow-style: none; /* For Internet Explorer and Edge */
}

.hide-scrollbar::-webkit-scrollbar {
    display: none; /* For Chrome, Safari, and Opera */
}

  /* Card hover effect */
  #subject-cards-container .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  #subject-cards-container .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }

  /* Title animation */
  @keyframes bounce {

    0%,
    20%,
    50%,
    80%,
    100% {
      transform: translateY(0);
    }

    40% {
      transform: translateY(-10px);
    }

    60% {
      transform: translateY(-5px);
    }
  }

  .title-bounce {
    animation: bounce 2s ease infinite;
  }
</style>

<body>
 <div class="min-h-screen flex flex-col md:flex-row relative overflow-x-hidden w-full">
    <!-- Include sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="min-h-screen w-full flex flex-col items-center justify-center p-2 xs:p-4 space-y-4 xs:space-y-6 flex-1 relative">
      <!-- Background div with absolute positioning -->
      <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-blue-500 bg-cover bg-center z-0"
        style="background-image: url('../pics/back1.jpg');"></div>

      <!-- Content wrapper with relative positioning -->
      <div class="relative z-10 w-full flex flex-col items-center space-y-4 xs:space-y-6  pt-10 md:pt-20">
        <!-- Title Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-3 w-full max-w-[95%] xs:max-w-sm text-center transform transition-transform duration-500 hover:scale-105 title-bounce border-l-8 border-yellow-500">
          <h1 class="text-xl xs:text-2xl font-bold text-gray-900">
            <?php echo htmlspecialchars($learningCard['card_title']); ?>
          </h1>
          <p class="text-gray-700 text-xs xs:text-sm mt-2">
            <?php echo htmlspecialchars($learningCard['description'] ?? 'Learn new vocabulary'); ?>
          </p>
        </div>

        <!-- Subject cards container -->
        <div id="subject-cards-container" class="grid grid-cols-1 sm:grid-cols-2 gap-2 xs:gap-4 w-full max-w-[95%] xs:max-w-3xl px-2 xs:px-4">
          <!-- Loading indicator -->
          <div class="col-span-full flex justify-center">
            <div class="animate-spin rounded-full h-8 w-8 xs:h-12 xs:w-12 border-t-2 border-b-2 border-white"></div>
          </div>
        </div>
      </div>
      <!-- Modal container -->
      <div id="modal-container" class=" w-full max-w-[95%] xs:max-w-3xl">
          <!-- Modals will be loaded here via AJAX -->
        </div>
    </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="javascript-handler/learn.js"></script>
  <script>
    // Enhance cards when they're loaded
    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
          // When new cards are added, add hover effects
          const cards = document.querySelectorAll('#subject-cards-container .card');
          cards.forEach(card => {
            card.classList.add('rounded-xl', 'shadow-lg', 'overflow-hidden');
          });
        }
      });
    });

    // Start observing the cards container
    observer.observe(document.getElementById('subject-cards-container'), { childList: true });

    // Add sound effects for interactions
    function playSound(type) {
      const sounds = {
        click: '../sounds/click.mp3',
        success: '../sounds/success.mp3',
        error: '../sounds/error.mp3'
      };

      if (sounds[type]) {
        const audio = new Audio(sounds[type]);
        audio.volume = 0.2;
        audio.play();
      }
    }
  </script>
</body>

</html>