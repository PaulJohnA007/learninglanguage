$(document).ready(function() {
    loadLearningCards();

    function loadLearningCards() {
        $.ajax({
            url: 'php-functions/user-progress.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const container = $('#cardsContainer');
                    container.empty();
                    
                    // Display a single progress card
                    container.append(`
                        <div class="grade-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Overall Learning Progress</h3>
                                <div class="mt-4">
                                    <div class="bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" 
                                             style="width: ${response.progress.progress}%">
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Progress: ${response.progress.progress}% 
                                        (${response.progress.completed_words}/${response.progress.total_words} words)
                                    </p>
                                </div>
                            </div>
                        </div>
                    `);

                    // Log the response for debugging
                    console.log('Progress data:', response.progress);
                } else {
                    $('#cardsContainer').html(`
                        <div class="col-span-full text-center py-8">
                            <h3 class="text-2xl font-bold text-gray-700">Could not load progress</h3>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading progress:', error);
                console.log('Response:', xhr.responseText);
                $('#cardsContainer').html(`
                    <div class="col-span-full text-center py-8">
                        <h3 class="text-2xl font-bold text-red-600">Error loading progress. Please try again later.</h3>
                    </div>
                `);
            }
        });
    }
});