document.getElementById('subjectCardForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get the card_id from the URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const cardId = urlParams.get('card_id');
    
    const formData = new FormData(this);
    formData.append('card_id', cardId); // Add the card_id to the form data
    
    fetch('php-functions/add-subject-card.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});

$(document).ready(function() {
    // Get card_id from URL
    const urlParams = new URLSearchParams(window.location.search);
    const cardId = urlParams.get('card_id');

    // Function to load subject cards
    function loadSubjectCards() {
        $.ajax({
            url: 'php-functions/fetch-subject-card.php',
            type: 'GET',
            data: { card_id: cardId },
            success: function(response) {
                if (response.success) {
                    const cardsContainer = $('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.gap-6');
                    cardsContainer.empty();

                    response.cards.forEach(card => {

                        const cleanImagePath = card.subject_card_image
                            .replace(/\\/g, '')
                            .replace('.../', '') 
                            .replace('../', ''); 
                        const cardHtml = `
                            <div class="dashboard-card p-4 animate-fade-in">
                                <div class="relative">
                                    <img src="${cleanImagePath}" alt="${card.title}" 
                                         class="w-full h-48 object-cover rounded-t-lg">
                                    <span class="absolute top-2 right-2 glass-effect px-3 py-1 rounded-full text-sm">
                                        ${card.subject_type}
                                    </span>
                                </div>
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold mb-2">${card.title}</h3>
                                    <p class="text-gray-600 text-sm mb-4">${card.description}</p>
                                    <div class="flex justify-between items-center">
                                        <button onclick="manageWords(${card.subject_id})" 
                                                class="text-blue-600 hover:text-blue-800">
                                            Manage Words
                                        </button>
                                        <div class="flex gap-2">
                                            <button onclick="editSubjectCard(${card.subject_id})" 
                                                    class="p-2 text-yellow-600 hover:text-yellow-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button onclick="deleteSubjectCard(${card.subject_id})" 
                                                    class="p-2 text-red-600 hover:text-red-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        cardsContainer.append(cardHtml);
                    });
                } else {
                    console.error('Error loading cards:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', error);
            }
        });
    }

    // Load cards on page load
    if (cardId) {
        loadSubjectCards();
    }
});

// Add this after your existing code

function editSubjectCard(subjectId) {
    $.ajax({
        url: 'php-functions/get-subject-card.php',
        type: 'GET',
        dataType: 'json',
        data: { subject_id: subjectId },
        success: function(response) {
            if (response.success) {
                const card = response.card;
                
                // Populate the modal fields
                $('#edit_subject_id').val(card.subject_id);
                $('#edit_card_id').val(card.card_id);
                $('#edit_title').val(card.title);
                $('#edit_subject_type').val(card.subject_type);
                $('#edit_description').val(card.description);
                
                // Show current image
                if (card.subject_card_image) {
                    const cleanImagePath = card.subject_card_image
                        .replace(/\\/g, '')
                        .replace('.../', '')
                        .replace('../', '');
                    
                    $('#current_image').html(`
                        <img src="${cleanImagePath}" alt="Current Image" 
                            class="img-thumbnail" style="max-height: 100px">
                        <p class="text-muted small">Current image will be kept if no new image is selected.</p>
                    `);
                }
                
                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editSubjectCardModal'));
                editModal.show();
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message || 'Failed to fetch subject card details',
                    icon: 'error'
                });
            }
        }
    });
}

// Handle save changes button click
$('#saveSubjectCardChanges').on('click', function() {
    const formData = new FormData($('#editSubjectCardForm')[0]);
    
    $.ajax({
        url: 'php-functions/update-subject-card.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Subject card updated successfully',
                    icon: 'success'
                }).then(() => {
                    $('#editSubjectCardModal').modal('hide');
                    loadSubjectCards(); // Reload the cards
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message || 'Failed to update subject card',
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to update subject card',
                icon: 'error'
            });
        }
    });
});

function deleteSubjectCard(subjectId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the subject card and all associated words. You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'php-functions/delete-subject-card.php',
                type: 'POST',
                data: { subject_id: subjectId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            'Subject card and associated words have been deleted.',
                            'success'
                        ).then(() => {
                            loadSubjectCards(); // Reload the cards
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message || 'Failed to delete subject card',
                            'error'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'Failed to delete subject card',
                        'error'
                    );
                }
            });
        }
    });
}