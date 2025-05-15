function loadWords() {
    const urlParams = new URLSearchParams(window.location.search);
    const subjectCardId = urlParams.get('subject_card_id');

    $.ajax({
        url: 'php-functions/fetch-words.php',
        type: 'GET',
        data: { subject_card_id: subjectCardId },
        success: function(response) {
            if (response.success) {
                const tbody = $('.data-table tbody');
                tbody.empty();

                response.words.forEach(word => {
                    const row = `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">${word.word}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${word.phonetic_spelling}</td>
                            <td class="px-6 py-4">${word.definition}</td>
                            <td class="px-6 py-4">${word.example_sentence}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="editWord(${word.word_id})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button onclick="deleteWord(${word.word_id})" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
        }
    });
}

$(document).ready(function() {
    // Get subject_card_id from URL
    const urlParams = new URLSearchParams(window.location.search);
    const subjectCardId = urlParams.get('subject_card_id');
    
    // Add hidden input for subject_card_id
    $('#wordForm').append(`<input type="hidden" name="subject_card_id" value="${subjectCardId}">`);

    // Handle form submission
    $('#wordForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: 'php-functions/add-words.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#wordForm')[0].reset();
                        loadWords();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    });

    



    // Load words on page load
    if (subjectCardId) {
        loadWords();
    }


// Handle save changes button click
$('#saveWordChanges').on('click', function() {
    const formData = new FormData($('#editWordForm')[0]);
    
    $.ajax({
        url: 'php-functions/update-word.php',
        type: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Word updated successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    $('#editWordModal').modal('hide');
                    loadWords(); // Reload the words table
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }
    });
});



});

function editWord(wordId) {
    $.ajax({
        url: 'php-functions/get-word.php',
        type: 'GET',
        dataType: 'json',
        data: { word_id: wordId },
        success: function(response) {
            if (response.success) {
                // Populate the modal with word data
                $('#edit_word_id').val(response.word.word_id);
                $('#edit_word').val(response.word.word);
                $('#edit_phonetic_spelling').val(response.word.phonetic_spelling);
                $('#edit_definition').val(response.word.definition);
                $('#edit_example_sentence').val(response.word.example_sentence);

                // Update the image preview by prepending 'admin/' to the word_image path
                if (response.word.word_image) {
                    $('#edit_word_image_preview').attr('src', 'admin/' + response.word.word_image).show();
                } else {
                    $('#edit_word_image_preview').hide();
                }

                // Show the modal using Bootstrap 5
                const modal = new bootstrap.Modal(document.getElementById('editWordModal'));
                modal.show();
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to fetch word details',
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to fetch word details',
                icon: 'error'
            });
        }
    });
}

// Preview the selected image in the modal
$('#edit_word_image').on('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#edit_word_image_preview').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
    } else {
        $('#edit_word_image_preview').hide();
    }
});


function deleteWord(wordId) {
    // Show confirmation dialog first
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with deletion
            $.ajax({
                url: 'php-functions/delete-word.php',
                type: 'POST',
                dataType: 'json',
                data: { word_id: wordId },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            'Word has been deleted.',
                            'success'
                        ).then(() => {
                            loadWords();
                            // location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message || 'Failed to delete word',
                            'error'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax Error:', error);
                    Swal.fire(
                        'Error!',
                        'Failed to delete word',
                        'error'
                    );
                }
            });
        }
    });
}