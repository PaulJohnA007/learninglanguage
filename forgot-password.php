<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="text/javascript">
    window.tailwind.config = {
      darkMode: ['class'],
      theme: {
        extend: {
          colors: {
            border: 'hsl(var(--border))',
            input: 'hsl(var(--input))',
            ring: 'hsl(var(--ring))',
            background: 'hsl(var(--background))',
            foreground: 'hsl(var(--foreground))',
            primary: {
              DEFAULT: 'hsl(var(--primary))',
              foreground: 'hsl(var(--primary-foreground))'
            },
            secondary: {
              DEFAULT: 'hsl(var(--secondary))',
              foreground: 'hsl(var(--secondary-foreground))'
            },
            destructive: {
              DEFAULT: 'hsl(var(--destructive))',
              foreground: 'hsl(var(--destructive-foreground))'
            },
            muted: {
              DEFAULT: 'hsl(var(--muted))',
              foreground: 'hsl(var(--muted-foreground))'
            },
            accent: {
              DEFAULT: 'hsl(var(--accent))',
              foreground: 'hsl(var(--accent-foreground))'
            },
            popover: {
              DEFAULT: 'hsl(var(--popover))',
              foreground: 'hsl(var(--popover-foreground))'
            },
            card: {
              DEFAULT: 'hsl(var(--card))',
              foreground: 'hsl(var(--card-foreground))'
            },
          },
        }
      }
    }
  </script>
  <style type="text/tailwindcss">
    @layer base {
        :root {
          --background: 0 0% 100%;
--foreground: 224 71.4% 4.1%;
--card: 0 0% 100%;
--card-foreground: 224 71.4% 4.1%;
--popover: 0 0% 100%;
--popover-foreground: 224 71.4% 4.1%;
--primary: 262.1 83.3% 57.8%;
--primary-foreground: 210 20% 98%;
--secondary: 220 14.3% 95.9%;
--secondary-foreground: 220.9 39.3% 11%;
--muted: 220 14.3% 95.9%;
--muted-foreground: 220 8.9% 46.1%;
--accent: 220 14.3% 95.9%;
--accent-foreground: 220.9 39.3% 11%;
--destructive: 0 84.2% 60.2%;
--destructive-foreground: 210 20% 98%;
--border: 220 13% 91%;
--input: 220 13% 91%;
--ring: 262.1 83.3% 57.8%;
        }
        .dark {
          --background: 224 71.4% 4.1%;
--foreground: 210 20% 98%;
--card: 224 71.4% 4.1%;
--card-foreground: 210 20% 98%;
--popover: 224 71.4% 4.1%;
--popover-foreground: 210 20% 98%;
--primary: 263.4 70% 50.4%;
--primary-foreground: 210 20% 98%;
--secondary: 215 27.9% 16.9%;
--secondary-foreground: 210 20% 98%;
--muted: 215 27.9% 16.9%;
--muted-foreground: 217.9 10.6% 64.9%;
--accent: 215 27.9% 16.9%;
--accent-foreground: 210 20% 98%;
--destructive: 0 62.8% 30.6%;
--destructive-foreground: 210 20% 98%;
--border: 215 27.9% 16.9%;
--input: 215 27.9% 16.9%;
--ring: 263.4 70% 50.4%;
        }
      }
    </style>
</head>

<body>
  <div class="min-h-screen flex flex-col items-center justify-center bg-cover bg-fixed"
    style="background-image: url('./pics/cartoon_school.jpg');">

    <div
      class="w-full max-w-md p-8 bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl transform transition-transform duration-300 hover:scale-105 border-4 border-yellow-400">

      <!-- Fun Header with Animation -->
      <div class="text-center mb-8">
        <h1
          class="text-4xl font-bold bg-gradient-to-r from-purple-600 via-pink-500 to-yellow-500 bg-clip-text text-transparent animate-pulse">
          Let's Learn English!
        </h1>
        <p class="text-gray-600 mt-2">Join the fun adventure!</p>
      </div>


      <div class="space-y-6">
        <form id="forgot-password" method="POST" class="space-y-4">
          <!-- Fun Welcome Back Message -->
          <div class="text-center mb-6">
            <h3 class="text-2xl font-bold text-purple-600">Lost your magic spell? 😢</h3>
          </div>

          <!-- Username Input -->
          <div class="relative group">
            <input type="email" name="email" placeholder="Your magical email" class="w-full px-6 py-4 bg-white/50 rounded-full border-2 border-blue-300 
              focus:border-blue-500 focus:ring-2 focus:ring-blue-300 transition-all duration-300
              group-hover:border-blue-400" required />
            <span class="absolute right-4 top-4 text-xl">🦸‍♂️</span>
          </div>



          <!-- Login Button -->
          <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 
  text-white rounded-full font-bold text-lg transform transition-all duration-300 
  hover:scale-105 hover:shadow-xl hover:from-indigo-600 hover:via-purple-600 hover:to-pink-600">
  <span class="button-text">Start Your Adventure! 🚀</span>
  <span class="spinner-border spinner-border-sm text-light ml-2" role="status" aria-hidden="true" style="display: none;"></span>
</button>
        </form>
      </div>

    </div>
  </div>

  
   <!-- OTP Verification Modal -->
   <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="otpModalLabel">Verify OTP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="verify-otp-form">
          <div class="mb-3">
            <label for="otp" class="form-label">Enter OTP</label>
            <div class="d-flex justify-content-between">
              <input type="text" class="form-control otp-input" maxlength="1" required>
              <input type="text" class="form-control otp-input" maxlength="1" required>
              <input type="text" class="form-control otp-input" maxlength="1" required>
              <input type="text" class="form-control otp-input" maxlength="1" required>
              <input type="text" class="form-control otp-input" maxlength="1" required>
              <input type="text" class="form-control otp-input" maxlength="1" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Verify</button>
        </form>
      </div>
    </div>
  </div>
</div>
<style>
  .otp-input {
    width: 3rem;
    height: 3rem;
    text-align: center;
    font-size: 1.5rem;
    margin-right: 0.5rem;
    border: 2px solid #3b82f6;
    border-radius: 0.5rem;
    transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
  }

  .otp-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 10px rgba(37, 99, 235, 0.5);
    outline: none;
  }

  .form-control {
    border: 2px solid #3b82f6;
    border-radius: 0.5rem;
    transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
  }

  .form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 10px rgba(37, 99, 235, 0.5);
    outline: none;
  }

  .modal-title {
    font-weight: 600;
  }

  .btn-primary {
    background-color: #3b82f6;
    border-color: #3b82f6;
    transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
  }

  .btn-primary:hover {
    background-color: #2563eb;
    border-color: #2563eb;
    box-shadow: 0 0 10px rgba(37, 99, 235, 0.5);
  }
</style>


<style>
  .otp-input {
    width: 3rem;
    height: 3rem;
    text-align: center;
    font-size: 1.5rem;
    margin-right: 0.5rem;
  }
</style>
<script>
  $(document).ready(function() {
    $('.otp-input').on('keyup', function(e) {
      if (e.keyCode >= 48 && e.keyCode <= 57) {
        $(this).next('.otp-input').focus();
      } else if (e.keyCode === 8) {
        $(this).prev('.otp-input').focus();
      }
    });
  });
</script>

<!-- New Password Modal -->
<div class="modal fade" id="newPasswordModal" tabindex="-1" aria-labelledby="newPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newPasswordModalLabel">Set New Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="new-password-form">
          <div class="mb-3">
            <label for="new-password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new-password" name="new-password" required>
          </div>
          <div class="mb-3">
            <label for="confirm-password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
          </div>
          <button type="submit" class="btn btn-primary">Set Password</button>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
<script src="assets/js/accounts.js"></script>

</html>