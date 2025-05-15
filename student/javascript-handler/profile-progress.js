$(document).ready(function() {
    // Fetch user progress via AJAX
    $.ajax({
        url: 'php-functions/profile-progress.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                console.error(response.error);
                return;
            }

            // Update progress bar and text
            $('.progress-bar').css('width', response.progress + '%');
            $('.progress-text').text(response.progress + '%');
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
        }
    });
});