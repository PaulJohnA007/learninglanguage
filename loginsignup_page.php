<?php
session_start();
require_once 'function/dbconnect.php';
require_once 'function/handler.php';
require_once 'function/control.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if (validateLoginInput($username, $password)) {
    $user = handleLogin($username, $password, $conn);

    if ($user) {
      redirectUser($user);
    } else {
      header("Location: loginsignup_page.php?error=1");
      exit();
    }
  }
}
?>

<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
  <script type="text/javascript">
    window.tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            'poppins': ['Poppins', 'sans-serif'],
          },
          colors: {
            'primary': '#58CC02',
            'success': 'rgb(2, 137, 204)',
            'primary-hover': '#46A302',
            'secondary': '#1CB0F6',
            'secondary-hover': '#0095D8',
            'accent': '#FF4B4B',
            'accent-hover': '#EA2B2B',
            'yellow': '#FFD900',
            'purple': '#CE82FF',
            'background': '#FFF9F0',
          }
        }
      }
    }
  </script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    .form-input {
      transition: all 0.3s ease;
    }

    .form-input:focus {
      border-color: #58CC02;
      box-shadow: 0 0 0 3px rgba(88, 204, 2, 0.2);
    }

    .tab-button {
      transition: all 0.3s ease;
    }

    .tab-button:hover {
      transform: translateY(-2px);
    }

    .form-container {
      background-color: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(10px);
    }

    .alert-animation {
      animation: slideIn 0.5s ease forwards;
    }

    @keyframes slideIn {
      0% {
        transform: translateX(100%);
        opacity: 0;
      }

      100% {
        transform: translateX(0);
        opacity: 1;
      }
    }

    .input-icon {
      transition: all 0.3s ease;
    }

    .form-input:focus+.input-icon {
      color: #58CC02;
    }
  </style>
</head>

<body class="font-poppins">
  <!-- Alert Notification -->
  <div id="successAlert"
    class="hidden fixed top-5 right-5 bg-success text-white px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-500">
    <div class="flex items-center space-x-2">
      <span class="text-2xl">✨</span>
      <p class="font-semibold">Successfully created account!</p>
    </div>
  </div>

  <div class="min-h-screen flex flex-col items-center justify-center bg-cover bg-fixed relative"
    style="background-image: url('./school.png');">
    <div class="absolute inset-0 bg-gradient-to-b from-primary/80 to-secondary/80 backdrop-blur-sm"></div>

    <div
      class="form-container w-full max-w-md p-8 rounded-3xl shadow-2xl transform transition-transform duration-300 hover:scale-[1.02] border-4 border-primary relative z-10">
      <!-- Fun Header with Animation -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-primary mb-2">
          Let's Learn English!
        </h1>
        <p class="text-gray-600">Join the fun adventure!</p>
      </div>

      <!-- Playful Tab Buttons -->
      <ul class="flex justify-around mb-6 gap-4">
        <li class="w-1/2">
          <button id="signup-tab"
            class="tab-button w-full py-3 bg-primary text-white rounded-full font-bold shadow-md hover:shadow-lg">
            Join Us! 🎈
          </button>
        </li>
        <li class="w-1/2">
          <button id="login-tab"
            class="tab-button w-full py-3 bg-secondary text-white rounded-full font-bold shadow-md hover:shadow-lg">
            Welcome Back! 🌟
          </button>
        </li>
      </ul>

      <!-- Form Containers with Fun Elements -->
      <div id="signup-form" class="space-y-4">
        <form action="sign-up.php" method="POST" enctype="multipart/form-data" class="space-y-4">
          <div class="relative">
            <input type="text" name="username" placeholder="Pick a cool username!"
              class="form-input w-full px-6 py-3 bg-white/80 rounded-full border-2 border-gray-300 focus:outline-none"
              required />
            <span class="input-icon absolute right-4 top-3 text-gray-500">👤</span>
          </div>

          <div class="relative">
            <input type="email" name="email" placeholder="Your email address"
              class="form-input w-full px-6 py-3 bg-white/80 rounded-full border-2 border-gray-300 focus:outline-none"
              required />
            <span class="input-icon absolute right-4 top-3 text-gray-500">📧</span>
          </div>

          <div class="relative">
            <input type="password" name="password" id="signup-password" placeholder="Create a secret password"
              class="form-input w-full px-6 py-3 bg-white/80 rounded-full border-2 border-gray-300 focus:outline-none"
              required />
            <span id="toggleSignupPassword"
              class="input-icon absolute right-4 top-3 cursor-pointer text-gray-500">🔑</span>
          </div>

          <div class="mb-4">
    <select id="grade_level" name="grade_level" class="mt-1 block w-full rounded-full border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required >
        <option value="" disabled selected>Select Grade Level</option>
        <option value="1">Grade 1</option>
        <option value="2">Grade 2</option>
        <option value="3">Grade 3</option>
        <option value="4">Grade 4</option>
        <option value="5">Grade 5</option>
        <option value="6">Grade 6</option>
    </select>
</div>

<div class="relative mb-4">
  <label for="profile_image" class="block mb-2 text-sm font-medium text-gray-700">Profile Picture</label>
  <div class="flex items-center">
    <label for="profile_image" class="cursor-pointer flex items-center justify-center px-4 py-2 bg-white border-2 border-gray-300 rounded-l-full hover:bg-gray-50 focus:outline-none focus:border-primary transition-colors">
      <span class="mr-2">📸</span>
      <span>Select Picture</span>
    </label>
    <div id="file-name" class="flex-1 px-4 py-2 bg-white/80 border-2 border-l-0 border-gray-300 rounded-r-full truncate">
      No picture selected
    </div>
    <input id="profile_image" type="file" name="profile_image" class="hidden" accept="image/*" onchange="updateFileName(this)"/>
  </div>
  <p class="mt-1 text-xs text-gray-500">Optional: JPG, PNG or GIF (Max. 5MB)</p>
</div>
<script>
  function updateFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameElement = document.getElementById('file-name');
    
    if (fileName) {
      fileNameElement.textContent = fileName;
      fileNameElement.classList.add('text-primary');
      
      // Show preview
      const reader = new FileReader();
      reader.onload = function(e) {
        // Check if preview exists, remove if it does
        const existingPreview = document.getElementById('image-preview');
        if (existingPreview) {
          existingPreview.remove();
        }
        
        // Create preview
        const preview = document.createElement('div');
        preview.id = 'image-preview';
        preview.className = 'mt-2 relative w-16 h-16 rounded-full overflow-hidden border-2 border-primary';
        
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'w-full h-full object-cover';
        
        preview.appendChild(img);
        input.parentElement.parentElement.appendChild(preview);
      };
      reader.readAsDataURL(input.files[0]);
    } else {
      fileNameElement.textContent = 'No picture selected';
      fileNameElement.classList.remove('text-primary');
      
      // Remove preview if exists
      const existingPreview = document.getElementById('image-preview');
      if (existingPreview) {
        existingPreview.remove();
      }
    }
  }
</script>

          <button type="submit"
            class="w-full px-6 py-3 bg-yellow text-gray-800 rounded-full font-bold transform transition hover:scale-105 hover:shadow-xl">
            Start Learning! 🚀
          </button>
        </form>
      </div>

      <div id="login-form" class="space-y-6 hidden">
        <form action="" method="POST" class="space-y-4">
          <!-- Fun Welcome Back Message -->
          <div class="text-center mb-6">
            <h3 class="text-2xl font-bold text-secondary">Welcome Back Explorer! 🌈</h3>
          </div>

          <!-- Username Input -->
          <div class="relative group">
            <input type="text" name="username" placeholder="Your magical username"
              class="form-input w-full px-6 py-4 bg-white/80 rounded-full border-2 border-gray-300 focus:outline-none"
              required />
            <span class="input-icon absolute right-4 top-4 text-gray-500 text-xl">🦸‍♂️</span>
          </div>

          <!-- Password Input -->
          <div class="relative group">
            <input type="password" name="password" id="password" placeholder="Your secret spell"
              class="form-input w-full px-6 py-4 bg-white/80 rounded-full border-2 border-gray-300 focus:outline-none"
              required />
            <span id="togglePassword"
              class="input-icon absolute right-4 top-4 text-gray-500 text-xl cursor-pointer">🔮</span>
          </div>

          <!-- Forgot Password Link -->
          <div class="text-center">
            <a href="forgot-password.php"
              class="inline-flex items-center text-secondary hover:text-secondary-hover hover:underline transition-colors duration-300">
              <span>Lost your magic spell?</span>
              <span class="ml-2">🤔</span>
            </a>
          </div>

          <!-- Login Button -->
          <button type="submit"
            class="w-full px-6 py-4 bg-primary text-white rounded-full font-bold text-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl hover:bg-primary-hover">
            Start Your Adventure! 🚀
          </button>
        </form>
      </div>

      <!-- Back to Home Link -->
      <div class="mt-6 text-center">
        <a href="index.html" class="text-secondary hover:text-secondary-hover flex items-center justify-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z"
              clip-rule="evenodd" />
          </svg>
          <span>Back to Home</span>
        </a>
      </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute bottom-10 left-10 w-24 h-24 hidden md:block">
      <img src="https://cdn-icons-png.flaticon.com/512/2995/2995440.png" alt="Mascot"
        class="w-full h-full animate-bounce">
    </div>
    <div class="absolute top-10 right-10 w-20 h-20 hidden md:block">
      <img src="https://cdn-icons-png.flaticon.com/512/3406/3406999.png" alt="Stars"
        class="w-full h-full animate-pulse">
    </div>
  </div>

  <script src="assets/js/accounts.js"></script>
  <script>
    const signupTab = document.getElementById('signup-tab');
    const loginTab = document.getElementById('login-tab');
    const signupForm = document.getElementById('signup-form');
    const loginForm = document.getElementById('login-form');

    loginTab.addEventListener('click', () => {
      signupForm.classList.add('hidden');
      loginForm.classList.remove('hidden');
      loginTab.classList.add('bg-secondary');
      signupTab.classList.remove('bg-primary');
      signupTab.classList.add('bg-gray-400');
      loginTab.classList.remove('bg-gray-400');
      signupTab.classList.add('opacity-75');
      loginTab.classList.remove('opacity-75');
    });

    signupTab.addEventListener('click', () => {
      loginForm.classList.add('hidden');
      signupForm.classList.remove('hidden');
      signupTab.classList.add('bg-primary');
      loginTab.classList.remove('bg-secondary');
      loginTab.classList.add('bg-gray-400');
      signupTab.classList.remove('bg-gray-400');
      loginTab.classList.add('opacity-75');
      signupTab.classList.remove('opacity-75');
    });

    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
      const passwordInput = document.getElementById('password');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        this.textContent = '👁️';
      } else {
        passwordInput.type = 'password';
        this.textContent = '🔮';
      }
    });

    document.getElementById('toggleSignupPassword').addEventListener('click', function () {
      const passwordInput = document.getElementById('signup-password');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        this.textContent = '👁️';
      } else {
        passwordInput.type = 'password';
        this.textContent = '🔑';
      }
    });

    function showSuccessAlert() {
      const alert = document.getElementById('successAlert');
      alert.classList.remove('bg-primary', 'bg-accent');
    alert.classList.add('bg-success'); 
      alert.classList.remove('hidden');
      alert.classList.add('alert-animation');

      setTimeout(() => {
        alert.classList.remove('alert-animation');
        alert.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
          alert.classList.add('hidden');
          alert.classList.remove('translate-x-full', 'opacity-0');
        }, 500);
      }, 3000);
    }

    function showErrorAlert(message) {
      const alert = document.getElementById('successAlert');
      // Change to red for error
      alert.classList.remove('bg-primary');
      alert.classList.add('bg-accent');
      // Change the message
      alert.querySelector('p').textContent = message;
      // Change the emoji
      alert.querySelector('span').textContent = '❌';

      // Show the alert
      alert.classList.remove('hidden');
      alert.classList.add('alert-animation');

      setTimeout(() => {
        alert.classList.remove('alert-animation');
        alert.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
          alert.classList.add('hidden');
          alert.classList.remove('translate-x-full', 'opacity-0');
          // Reset back to success style
          alert.classList.remove('bg-accent');
          alert.classList.add('bg-primary');
          alert.querySelector('p').textContent = 'Successfully created account!';
          alert.querySelector('span').textContent = '✨';
        }, 500);
      }, 3000);
    }

    // Check for URL parameters to show appropriate alerts
    window.onload = function () {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('success') === '1') {
        showSuccessAlert();
      }
      if (urlParams.get('error') === '1') {
        showErrorAlert('Login failed. Please check your credentials.');
        loginTab.click(); // Switch to login tab
      }
    };

    // Add some fun animations
    document.querySelectorAll('.form-input').forEach(input => {
      input.addEventListener('focus', function () {
        this.classList.add('scale-105');
        this.style.transition = 'all 0.3s ease';
      });

      input.addEventListener('blur', function () {
        this.classList.remove('scale-105');
      });
    });

    // Add confetti effect on successful form submission
    document.querySelector('#signup-form form').addEventListener('submit', function (e) {
      // Form validation would go here
      // This is just for demo purposes - normally you'd validate on the server
      const username = this.querySelector('input[name="username"]').value;
      const email = this.querySelector('input[name="email"]').value;
      const password = this.querySelector('input[name="password"]').value;

      if (!username || !email || !password) {
        e.preventDefault();
        showErrorAlert('Please fill in all required fields!');
        return false;
      }

      // Email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        e.preventDefault();
        showErrorAlert('Please enter a valid email address!');
        return false;
      }

      // Password strength validation
      if (password.length < 6) {
        e.preventDefault();
        showErrorAlert('Password must be at least 6 characters long!');
        return false;
      }

      // If all validations pass, the form will submit normally
    });

    // Add floating labels effect
    document.querySelectorAll('.form-input').forEach(input => {
      input.addEventListener('input', function () {
        if (this.value.length > 0) {
          this.classList.add('border-primary');
        } else {
          this.classList.remove('border-primary');
        }
      });
    });
  </script>

  <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
    <script>
      Swal.fire({
        title: 'Oops!',
        text: 'Login failed. Please check your username and password.',
        icon: 'error',
        confirmButtonText: 'Try Again',
        confirmButtonColor: '#58CC02',
        background: '#FFF9F0',
        iconColor: '#FF4B4B',
        customClass: {
          title: 'font-poppins font-bold text-xl',
          content: 'font-poppins',
          confirmButton: 'font-poppins font-bold'
        }
      });
    </script>
  <?php endif; ?>

  <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
    <script>
      Swal.fire({
        title: 'Hooray!',
        text: 'Your account has been created successfully! Let\'s start learning!',
        icon: 'success',
        confirmButtonText: 'Let\'s Go!',
        confirmButtonColor: '#58CC02',
        background: '#FFF9F0',
        iconColor: '#58CC02',
        customClass: {
          title: 'font-poppins font-bold text-xl',
          content: 'font-poppins',
          confirmButton: 'font-poppins font-bold'
        }
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('login-tab').click();
        }
      });
    </script>
  <?php endif; ?>

  <!-- Add a fun animated character at the bottom -->
  <div class="fixed bottom-0 right-0 w-32 h-32 md:w-48 md:h-48 z-10 hidden md:block pointer-events-none">
    <img src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png" alt="Learning Buddy"
      class="w-full h-full animate-bounce" style="animation-duration: 3s;">
  </div>
</body>

</html>