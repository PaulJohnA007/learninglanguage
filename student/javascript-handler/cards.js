$(document).ready(function() {
    loadCards();

    function loadCards() {
        $.ajax({
            url: 'php-functions/cards.php',
            method: 'GET',
            success: function(response) {
                $('#loadingSpinner').hide();
                const $container = $('#cardsContainer');
                
                if (response.cards.length === 0) {
                    $container.html(getEmptyStateHTML());
                } else {
                    const cardsHTML = response.cards.map(card => {
                        // Use the is_locked property from the server response
                        return generateCardHTML(card, card.is_locked);
                    }).join('');
                    
                    $container.html(cardsHTML);
                }
                
                $container.show();
                initializeCardEvents();
            },
            error: function() {
                $('#loadingSpinner').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load learning cards'
                });
            }
        });
    }

    function generateCardHTML(card, isDisabled) {
        const progress = card.progress;
        return `
            <div class="grade-card bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 
                ${isDisabled ? 'opacity-70 cursor-not-allowed' : ''}">
                <div class="relative">
                    ${isDisabled ? getLockOverlayHTML() : ''}
                    <img src="../admin/${card.card_image}" 
                        alt="${card.card_title}"
                        class="w-full h-48 object-cover ${isDisabled ? 'filter blur-sm' : ''}">
                    <div class="absolute top-3 right-3 bg-yellow-400 text-black px-3 py-1 rounded-full text-sm font-bold shadow-md">
                        ${card.difficulty_level}
                    </div>
                    ${progress === 100 ? getCompletedBadgeHTML() : ''}
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">${card.card_title}</h3>
                    <p class="text-gray-600 mb-4 text-sm">${card.description}</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-medium">
                            ${card.category}
                        </span>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                            ${card.total_words} words
                        </span>
                    </div>
                    ${getProgressBarHTML(card)}
                    ${getCardActionsHTML(card, isDisabled)}
                </div>
            </div>`;
    }

    function getLockOverlayHTML() {
        return `
            <div class="absolute inset-0 bg-gray-800 bg-opacity-50 z-10 flex items-center justify-center rounded-t-2xl backdrop-blur-sm">
                <div class="bg-white px-4 py-3 rounded-xl shadow-lg transform rotate-3 hover:rotate-0 transition-transform duration-300">
                    <span class="text-gray-800 font-semibold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Complete previous card first!
                    </span>
                </div>
            </div>`;
    }

    function getCompletedBadgeHTML() {
        return `
            <div class="absolute top-3 left-3 bg-primary text-white px-3 py-1 rounded-full text-sm font-bold shadow-md flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Completed
            </div>`;
    }

    function getProgressBarHTML(card) {
        const progress = card.progress;
        return `
            <div class="mt-4 progressbar w-full group relative">
                <div class="bg-gray-200 rounded-full h-3 cursor-pointer overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="progress-bar h-full rounded-full relative" style="width: ${progress}%">
                        <div class="absolute inset-0 bg-white/20 animate-shimmer"></div>
                    </div>
                </div>
                ${getProgressTooltipHTML(card)}
                ${getProgressIndicatorsHTML(progress)}
            </div>`;
    }

    function getProgressTooltipHTML(card) {
        return `
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-4 py-2 rounded-xl text-sm z-10">
                <div class="flex flex-col items-center">
                    <span class="font-bold">${card.progress}% Complete</span>
                    <span class="text-xs">${card.completed_words}/${card.total_words} words</span>
                </div>
                <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-2 h-2 bg-gray-800"></div>
            </div>`;
    }

    function getProgressIndicatorsHTML(progress) {
        return `
            <div class="flex justify-between items-center mt-2">
                <div class="flex items-center gap-1">
                </div>
                <span class="text-sm font-medium ${progress === 100 ? 'text-primary' : 'text-gray-600'}">
                    ${progress === 100 ? '✨ Mastered!' : 'In Progress'}
                </span>
            </div>`;
    }

    function getCardActionsHTML(card, isDisabled) {
        if (!isDisabled) {
            return `
                <div class="mt-6 flex justify-end">
                    <a href="learn.php?card_id=${card.card_id}" 
                        class="bg-secondary hover:bg-blue-600 text-white px-5 py-2 rounded-lg transition-colors duration-300 flex items-center shadow-md">
                        <span>Start Learning</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>`;
        }
        return `
            <div class="mt-6 flex justify-end">
                <button disabled class="bg-gray-400 cursor-not-allowed text-white px-5 py-2 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    Locked
                </button>
            </div>`;
    }


});