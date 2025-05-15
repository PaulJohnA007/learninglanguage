$(document).ready(function() {
    $.ajax({
        url: 'php-functions/get-total-user.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                console.error(response.error);
                $('#total-users-count').text('Error');
                return;
            }

            // Update total users count
            $('#total-users-count').text(response.total_users);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            $('#total-users-count').text('Error');
        }
    });
});

    $(document).ready(function() {
        $.ajax({
            url: 'php-functions/total-subject-card.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error(response.error);
                    $('#total-subject-cards-count').text('Error');
                    return;
                }

                // Update total subject cards count
                $('#total-subject-cards-count').text(response.total_subject_cards);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#total-subject-cards-count').text('Error');
            }
        });
    });

    $(document).ready(function() {
        $.ajax({
            url: 'php-functions/get-total-words.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error(response.error);
                    $('#total-words-count').text('Error');
                    return;
                }

                // Update total words count
                $('#total-words-count').text(response.total_words);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#total-words-count').text('Error');
            }
        });
    });

    $(document).ready(function () {

        $.ajax({
            url: 'php-functions/average-rating.php', 
            type: 'GET',
            dataType: 'json',
            success: function (response) {
         
                if (response.averageProgress !== undefined) {
                    $('#average-progress-count').text(response.averageProgress + '%');
                } else {
                    $('#average-progress-count').text('0%');
                }
            },
            error: function () {
                // Handle errors
                $('#average-progress-count').text('Error');
            }
        });
    });