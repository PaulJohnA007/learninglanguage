$(document).ready(function() {
    $('#togglePassword').on('click', function() {
      const passwordField = $('#password');
      const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
      passwordField.attr('type', type);
      
      // Toggle the icon
      $(this).text(type === 'password' ? '🔮' : '🙈');
    });
  });

  $(document).ready(function() {
    $('#toggleSignupPassword').on('click', function() {
      const passwordField = $('#signup-password');
      const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
      passwordField.attr('type', type);
      
      // Toggle the icon
      $(this).text(type === 'password' ? '🔑' : '🙈');
    });
  });

  $(document).ready(function() {
    $('#forgot-password').on('submit', function(e) {
      e.preventDefault();

      var email = $('input[name="email"]').val();
      var $button = $(this).find('button[type="submit"]');
      var $spinner = $button.find('.spinner-border');
      var $buttonText = $button.find('.button-text');

      // Show the spinner and disable the button
      $spinner.show();
      $buttonText.hide();
      $button.prop('disabled', true);

      $.ajax({
          url: 'function/forgot-password.php',
          method: 'POST',
          data: { email: email },
          dataType: 'json',
          success: function(response) {
              // Hide the spinner and enable the button
              $spinner.hide();
              $buttonText.show();
              $button.prop('disabled', false);

              if (response.success) {
                  Swal.fire({
                      title: 'Success!',
                      text: response.message,
                      icon: 'success',
                      confirmButtonText: 'OK'
                  }).then((result) => {
                      if (result.isConfirmed) {
                          $('#otpModal').modal('show');
                      }
                  });
              } else {
                  Swal.fire({
                      title: 'Error!',
                      text: response.message,
                      icon: 'error',
                      confirmButtonText: 'OK'
                  });
              }
          },
          error: function(xhr, status, error) {
              // Hide the spinner and enable the button
              $spinner.hide();
              $buttonText.show();
              $button.prop('disabled', false);

              Swal.fire({
                  title: 'Error!',
                  text: 'An error occurred while sending the OTP. Please try again.',
                  icon: 'error',
                  confirmButtonText: 'OK'
              });
          }
      });
  });

    $('#verify-otp-form').on('submit', function(e) {
      e.preventDefault();

      // Concatenate the values of the individual OTP input fields
      var otp = '';
      $('.otp-input').each(function() {
          otp += $(this).val();
      });

      $.ajax({
          url: 'function/verify-otp.php',
          method: 'POST',
          data: { otp: otp },
          dataType: 'json',
          success: function(response) {
              if (response.success) {
                  Swal.fire({
                      title: 'Success!',
                      text: response.message,
                      icon: 'success',
                      confirmButtonText: 'OK'
                  }).then((result) => {
                      if (result.isConfirmed) {
                          $('#otpModal').modal('hide');
                          $('#newPasswordModal').modal('show');
                      }
                  });
              } else {
                  Swal.fire({
                      title: 'Error!',
                      text: response.message,
                      icon: 'error',
                      confirmButtonText: 'OK'
                  });
              }
          },
          error: function(xhr, status, error) {
              Swal.fire({
                  title: 'Error!',
                  text: 'An error occurred while verifying the OTP. Please try again.',
                  icon: 'error',
                  confirmButtonText: 'OK'
              });
          }
      });
  });
  $('#new-password-form').on('submit', function(e) {
    e.preventDefault();

    var newPassword = $('#new-password').val();
    var confirmPassword = $('#confirm-password').val();

    if (newPassword !== confirmPassword) {
        Swal.fire({
            title: 'Error!',
            text: 'Passwords do not match. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    $.ajax({
        url: 'function/set-new-password.php',
        method: 'POST',
        data: { password: newPassword },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'loginsignup_page.php';
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while setting the new password. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
});
});