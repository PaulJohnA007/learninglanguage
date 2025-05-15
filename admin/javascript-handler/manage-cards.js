$(document).ready(function() {
    $('#learning-card').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: 'php-functions/add-learning-card.php',
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
                        $('#learning-card')[0].reset();
                        fetchCards(); // Refresh the cards display
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
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

       // Function to fetch and display cards
       function fetchCards() {
        $.ajax({
            url: 'php-functions/fetch-learning-card.php',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const cardsContainer = $('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.gap-6');
                    cardsContainer.empty(); // Clear existing cards

                    response.cards.forEach(card => {
                        const cardHtml = `
                            <div class="dashboard-card p-4 animate-fade-in">
                                <div class="relative">
                                    <img src="${card.card_image}" alt="${card.card_title}"
                                        class="w-full h-48 object-cover rounded-t-lg">
                                    <span class="absolute top-2 right-2 glass-effect px-3 py-1 rounded-full text-sm">
                                        ${card.category}
                                    </span>
                                </div>
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold mb-2">${card.card_title}</h3>
                                    <p class="text-gray-600 text-sm mb-2">Level: ${card.difficulty_level}</p>
                                    <p class="text-gray-600 text-sm mb-4">${card.description}</p>
                                    <div class="flex justify-between items-center">
                                        <button onclick="window.location.href='manage_cards_list.php?card_id=${card.card_id}'"
                                            class="text-blue-600 hover:text-blue-800">View Card Words</button>
                                        <div class="flex gap-2">
                                            <button onclick="updateCard(${card.card_id})"
                                                class="p-2 text-yellow-600 hover:text-yellow-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button onclick="deleteCard(${card.card_id})" 
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
                    console.error('Error fetching cards:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', error);
            }
        });
    }

    // Initial load of cards
    fetchCards();

});

// Add this after your existing code

function updateCard(cardId) {
    $.ajax({
        url: 'php-functions/get-learningcard.php',
        type: 'GET',
        data: { card_id: cardId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const card = response.card;
                
                // Populate the modal fields
                $('#edit_card_id').val(card.card_id);
                $('#edit_card_title').val(card.card_title);
                $('#edit_category').val(card.category);
                $('#edit_difficulty_level').val(card.difficulty_level);
                $('#edit_description').val(card.description);
                
                // Show current image
                if (card.card_image) {
                    $('#current_image').html(`
                        <img src="${card.card_image}" alt="Current Image" 
                            class="img-thumbnail" style="max-height: 100px">
                        <p class="text-muted small">Current image will be kept if no new image is selected.</p>
                    `);
                }
                
                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editCardModal'));
                editModal.show();
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message || 'Failed to fetch card details',
                    icon: 'error'
                });
            }
        }
    });
}

// Handle save changes button click
$('#saveCardChanges').on('click', function() {
    const formData = new FormData($('#editCardForm')[0]);
    
    $.ajax({
        url: 'php-functions/update-learningcard.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Card updated successfully',
                    icon: 'success'
                }).then(() => {
                    $('#editCardModal').modal('hide');
                    fetchCards();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message || 'Failed to update card',
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to update card',
                icon: 'error'
            });
        }
    });
});

function deleteCard(cardId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this! This will also delete all associated words and progress.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'php-functions/delete-learning-card.php',
                type: 'POST',
                data: { card_id: cardId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            'Learning card has been deleted.',
                            'success'
                        ).then(() => {
                            location.reload(); // Reload the page on success
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message || 'Failed to delete card',
                            'error'
                        ).then(() => {
                            location.reload(); // Reload the page on error
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'An unexpected error occurred',
                        'error'
                    ).then(() => {
                        location.reload(); // Reload the page on AJAX error
                    });
                    console.error('Ajax error:', error);
                }
            });
        }
    });
}