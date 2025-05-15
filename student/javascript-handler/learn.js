$(document).ready(function() {
    // Get card_id from URL
    const urlParams = new URLSearchParams(window.location.search);
    const cardId = urlParams.get('card_id');
    
    // Store global data
    let subjectData = {};
    let recognitionInstance = null;
    let isRecording = false;
    
    // CALL LOAD SUBJECTS FUNCTION
    loadSubjects();
    
    // Burger menu handler
    $('#burgerMenu').click(function() {
        const sidebar = $('#sidebar');
        if (sidebar.css('max-height') !== '0px') {
            sidebar.css('max-height', '0');
        } else {
            sidebar.css('max-height', sidebar.prop('scrollHeight') + 'px');
        }
    });
    
    // FUNCTION TO LOAD SUBJECTS
    function loadSubjects() {
        $.ajax({
            url: 'php-functions/get_subjects.php',
            type: 'GET',
            data: { card_id: cardId },
            dataType: 'json',
            success: function(subjects) {
                // Clear existing content
                $('#subject-cards-container').empty();
                $('#modal-container').empty();
                
                // Render subject cards
                renderSubjectCards(subjects);
                
                // Render modals
                renderModals(subjects);
                
                // Attach event listeners
                attachEventListeners();
            },
            error: function(xhr, status, error) {
                console.error('Error loading subjects:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to load subjects. Please refresh the page.',
                    icon: 'error'
                });
            }
        });
    }
    
    // Function to render subject cards
   
    function renderSubjectCards(subjects) {
        const container = $('#subject-cards-container');
        container.empty();
        
        if (subjects.length <= 3) {
            container.removeClass('grid grid-cols-1 md:grid-cols-2 gap-4');
            container.addClass('flex flex-col sm:flex-row flex-wrap justify-center gap-4');
            container.css({
                'padding': '20px',
                'overflow': 'visible',
                'min-height': '400px'
            });
    
            subjects.forEach(function(subject) {
                const subjectCard = `
                    <div id="${subject.subject_type}Section" data-subject-id="${subject.subject_id}"
                        class="cursor-pointer bg-white rounded-2xl p-4 flex flex-col items-center transform 
                        transition-transform duration-500 hover:rotate-6 hover:scale-105 border-2 border-[#58CC02]
                        w-full sm:w-[300px] max-w-[95%] mx-auto mb-4 sm:mb-0"
                    >
                        <img class="rounded-2xl w-full h-[200px] object-cover mb-4"
                            src="../admin/subject-img/${subject.subject_card_image.split('/').pop()}"
                            alt="${htmlEscape(subject.title)}">
                        <h3 class="text-lg font-semibold text-gray-800">
                            ${htmlEscape(subject.title)}
                        </h3>
                        <p class="text-sm text-gray-600">
                            ${htmlEscape(subject.description)}
                        </p>
                        <div class="mt-4 progressbar w-full group relative">
                            <div class="bg-gray-200 rounded-full h-3 cursor-pointer overflow-hidden">
                                <div class="bg-[#58CC02] h-full rounded-full transition-all duration-500 relative"
                                    style="width: ${Math.round(subject.progress)}%">
                                    <div class="absolute inset-0 bg-white/20 animate-shimmer"></div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Progress: ${Math.round(subject.progress)}%</p>
                        </div>
                    </div>
                `;
                
                container.append(subjectCard);
            });
        } else {
            container.removeClass('flex flex-row flex-wrap justify-center gap-4');
            container.addClass('grid grid-cols-1 sm:grid-cols-2 gap-4');
            
            subjects.forEach(function(subject) {
                const subjectCard = `
                    <div id="${subject.subject_type}Section" data-subject-id="${subject.subject_id}"
                        class="cursor-pointer bg-white rounded-2xl p-4 flex flex-col items-center transform 
                        transition-transform duration-500 hover:rotate-6 hover:scale-105 border-2 border-[#58CC02]
                        w-full max-w-[95%] mx-auto"
                    >
                        <img class="rounded-2xl w-full h-[200px] object-cover mb-4"
                            src="../admin/${subject.subject_card_image.split('/').pop()}"
                            alt="${htmlEscape(subject.title)}">
                        <h3 class="text-lg font-semibold text-gray-800">
                            ${htmlEscape(subject.title)}
                        </h3>
                        <p class="text-sm text-gray-600">
                            ${htmlEscape(subject.description)}
                        </p>
                        <div class="mt-4 progressbar w-full group relative">
                            <div class="bg-gray-200 rounded-full h-3 cursor-pointer overflow-hidden">
                                <div class="bg-[#58CC02] h-full rounded-full transition-all duration-500 relative"
                                    style="width: ${Math.round(subject.progress)}%">
                                    <div class="absolute inset-0 bg-white/20 animate-shimmer"></div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Progress: ${Math.round(subject.progress)}%</p>
                        </div>
                    </div>
                `;
                
                container.append(subjectCard);
            });
        }
    }
    
// Function to render modals
function renderModals(subjects) {
    subjects.forEach(function(subject) {
        const modalId = `modal_${subject.subject_id}`;
        
       // CREATE MODAL FOR WORDS TO PRONOUNCE - WITH RESPONSIVE CLASSES
       const modalShell = `
       <div id="${modalId}" class="modal">
           <div class="modal-content w-full max-w-[95%] sm:max-w-[90%] md:max-w-[80%] lg:max-w-[70%]">
               <span class="close-button">×</span>
               <div class="modal-header">
                   <h2 class="modal-title text-lg sm:text-xl md:text-2xl">${subject.title.toUpperCase()} SECTION</h2>
               </div>
               <div class="modal-body hide-scrollbar overflow-auto max-h-[80vh]" id="modal-body-${subject.subject_id}">
                   <div class="text-center p-4 sm:p-8">
                       <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-500 mx-auto"></div>
                       <p class="mt-4">Loading content...</p>
                   </div>
               </div>
           </div>
       </div>
   `;
        
        $('#modal-container').append(modalShell);
    });
}

    
// FUNCTION TO LOAD THE MODAL WITH THE WORDS
function loadModalContent(subjectId) {
        $.ajax({
            url: 'php-functions/get_words.php',
            type: 'GET',
            data: { subject_id: subjectId },
            dataType: 'json',
            success: function(words) {
                subjectData[subjectId] = {
                    words: words,
                    currentIndex: 0
                };
                
                if (words && words.length > 0) {
                    renderWordContent(subjectId, words[0]);
                } else {
                    loadCompletedWords(subjectId);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading words:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Could not load words for this section. Please try again.',
                    icon: 'error'
                });
            }
        });
    }
// FUNCTION TO LOAD THE MODAL WITH THE WORDS
    
// FUNCTION TO RENDER WORD CONTENTS
function renderWordContent(subjectId, word) {
        const wordContent = `
        <div class="modal-content-center flex justify-center items-center flex-col">
    <h2 class="text-xl font-bold mb-2">SAY THE FOLLOWING WORD:</h2>
    <div class="text-lg font-bold mb-10 mt-2" style="font-size: 35px;" id="dynamicWord_${subjectId}" data-word-id="${word.word_id}">
        ${htmlEscape(word.word)}
    </div>
    <img src="../${word.word_image}" alt="Word Image" class="word-image w-[400px] h-[200px]" />
</div>
            <div class="modal-content-left">
                <div class="text-lg font-semibold">SOUNDS LIKE:</div>
                <div class="text-lg" id="phoneticTranscription_${subjectId}">
                    ${htmlEscape(word.phonetic_spelling)}
                    <span id="speakButton_${subjectId}" class="inline-block ml-2 cursor-pointer speakButton">🔊</span>
                </div>
            </div>
            <div class="modal-content-bottom">
                <div class="text-lg font-semibold">Example:</div>
                <div class="text-lg italic">
                    ${htmlEscape(word.example_sentence)}
                </div>
                <div class="text-lg">
                    <span id="micButton_${subjectId}" class="inline-block ml-2 mt-4 cursor-pointer micButton">🔈</span>
                </div>
            </div>
            <div class="mic-animation hidden" id="micAnimation_${subjectId}">
                <div class="mic-pulse"></div>
                <div class="mic-status">Listening...</div>
            </div>
        `;
        
        $(`#modal-body-${subjectId}`).html(wordContent);
        
        attachSpeechHandlers(subjectId);
    }
// FUNCTION TO RENDER WORD CONTENTS


// FFUNCTION TO LOAD COMPLETED WORDS
    function loadCompletedWords(subjectId) {
        $.ajax({
            url: 'php-functions/get_completed_words.php',
            type: 'GET',
            data: { subject_id: subjectId },
            dataType: 'json',
            success: function(completedWords) {
                renderCompletionMessage(subjectId, completedWords);

                attachCompletedWordsHandlers();
            },
            error: function(xhr, status, error) {
                console.error('Error loading completed words:', error);
                
                renderCompletionMessage(subjectId, []);
                attachCompletedWordsHandlers();
            }
        });
    }
// FFUNCTION TO LOAD COMPLETED WORDS

// COMPLETED WORD SPEAK BUTTON 

function attachCompletedWordsHandlers() {
    $(document).off('click', '.speakButtonCompleted');
    // Attach new handlers
    $(document).on('click', '.speakButtonCompleted', function() {
        const wordElement = $(this).closest('li').find('span:first-child');
        if (wordElement.length) {
            speakText(wordElement.text().trim());
        }
    });
}
// COMPLETED WORD SPEAK BUTTON 
    
// FUNCTION FOR COMPLETION MESSAGE OF ALL WORDS
    function renderCompletionMessage(subjectId, completedWords) {
        let completionContent = `
            <div class="completion-message text-center py-8">
                <div class="text-4xl mb-4">🎉</div>
                <h2 class="text-2xl font-bold text-green-600 mb-4">Congratulations!</h2>
                <p class="text-lg mb-4">You've completed all words in this section!</p>
                <a href="dashboardselect.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg 
                  transition-colors duration-300 inline-block">
                  Back to Dashboard
                </a>
            </div>
        `;
        
        if (completedWords && completedWords.length > 0) {
            const subjectTitle = $(`[data-subject-id="${subjectId}"] h3`).text();
            
            completionContent += `
                <div class="completed-words-section mt-6">
                    <h3 class="text-xl font-bold text-gray-700 mb-4">Words You've Completed in ${htmlEscape(subjectTitle)}:</h3>
                    <ul class="list-disc list-inside text-lg text-gray-600">
            `;
            
            completedWords.forEach(function(word, index) {
                completionContent += `
                    <li class="flex items-center">
                        <span id="dynamicWordCompleted_${subjectId}_${index}" class="mr-2">
                            ${htmlEscape(word.word)}
                        </span>
                        <span id="speakButtonCompleted_${subjectId}_${index}" 
                            class="inline-block ml-2 cursor-pointer speakButtonCompleted">🔊</span>
                    </li>
                `;
            });
            
            completionContent += `
                    </ul>
                </div>
            `;
        }
        
        $(`#modal-body-${subjectId}`).html(completionContent);
    }
// FUNCTION FOR COMPLETION MESSAGE OF ALL WORDS
    
// FUNCTION TO SHOW NEXT WORD
function showNextWord(subjectId) {
        const subject = subjectData[subjectId];
        if (!subject) return;
        
        subject.currentIndex++;
        
        if (subject.currentIndex < subject.words.length) {
            const nextWord = subject.words[subject.currentIndex];
            const modalBody = $(`#modal-body-${subjectId}`);
            
            modalBody.css('animation', 'slideOutLeft 0.5s forwards');
            
            setTimeout(() => {
                renderWordContent(subjectId, nextWord);
                modalBody.css('animation', 'slideInRight 0.5s forwards');
            }, 500);
        } else {
            loadCompletedWords(subjectId);
        }
    }
// FUNCTION TO SHOW NEXT WORD   

// FUNCTION TO REFRESH PROGRESS
function refreshProgress(subjectId) {
        $.ajax({
            url: 'php-functions/get_subjects.php',
            type: 'GET',
            data: { card_id: cardId },
            dataType: 'json',
            success: function(subjects) {
                // Find the subject with the matching ID
                const subject = subjects.find(s => s.subject_id == subjectId);
                if (subject) {
                    updateProgressUI(subjectId, subject.progress);
                }
            }
        });
    }
// FUNCTION TO REFRESH PROGRESS

// FUNCTION TO REFRESH PROGRESS UI
    function updateProgressUI(subjectId, progress) {
        const roundedProgress = Math.round(progress);
        const subjectSection = $(`[data-subject-id="${subjectId}"]`);
        
        subjectSection.find('.bg-\\[\\#58CC02\\]').css('width', `${roundedProgress}%`);
        
        subjectSection.find('.text-sm.text-gray-600').text(`Progress: ${roundedProgress}%`);
    }
// FUNCTION TO REFRESH PROGRESS UI
    

// FUNCTION TO ATTACH EVENT LISTENERS
function attachEventListeners() {
    // Remove previous handlers to prevent duplication
    $('[id$="Section"]').off('click');
    $('.close-button').off('click');
    $(window).off('click.modalClose');
    
    // Subject card click event
    $('[id$="Section"]').on('click', function() {
        const subjectId = $(this).data('subject-id');
        const modal = $(`#modal_${subjectId}`);
        
        // Clean up previous state before loading new content
        cleanupPreviousState();
        
        // Load content for this subject
        loadModalContent(subjectId);
        
        // Show the modal
        modal.css('display', 'flex');
    });
    
    // Close button event
    $('.close-button').on('click', function() {
        const modal = $(this).closest('.modal');
        const subjectId = modal.attr('id').split('_')[1];
        
        // Clean up state before closing
        cleanupPreviousState();
        
        // Hide the modal
        modal.css('display', 'none');
        
        // Refresh progress data
        refreshProgress(subjectId);
    });
    
    // Close modal when clicking outside
    $(window).on('click.modalClose', function(event) {
        if ($(event.target).hasClass('modal')) {
            // Clean up state before closing
            cleanupPreviousState();
            
            $(event.target).css('display', 'none');
        }
    });
}
// FUNCTION TO ATTACH EVENT LISTENERS

// FUNCTION TO CLEANUP PREVIOUS STATE(FOR DYNAMIC MIC TOGGLE)
function cleanupPreviousState() {
    // Stop any active speech recognition
    stopRecognition();
    
    // Clear any active speech synthesis
    window.speechSynthesis.cancel();
    
    // Remove all dynamic event handlers to prevent duplication
    $(document).off('click', '.speakButton');
    $(document).off('click', '.speakButtonCompleted');
    $(document).off('click', '.micButton');
}
// FUNCTION TO CLEANUP PREVIOUS STATE(FOR DYNAMIC MIC TOGGLE)


// FUNCTION FOR ATTACH SPEECH HANDLERS
function attachSpeechHandlers(subjectId) {
    // First, clean up any existing handlers to prevent duplicates
    $(document).off('click', `#speakButton_${subjectId}, .speakButton`);
    $(document).off('click', '.speakButtonCompleted');
    $(document).off('click', `#micButton_${subjectId}, .micButton`);
    
    $(document).on('click', `#speakButton_${subjectId}, .speakButton`, function() {
        const wordElement = $(`#dynamicWord_${subjectId}`);
        if (wordElement.length) {
            speakText(wordElement.text().trim());
        }
    });
    
    // Completed words speak button handler
    $(document).on('click', '.speakButtonCompleted', function() {
        const wordElement = $(this).closest('li').find('span:first-child');
        if (wordElement.length) {
            speakText(wordElement.text().trim());
        }
    });
    
    // Mic button handler
    $(document).on('click', `#micButton_${subjectId}, .micButton`, function() {
        // First check if we're already recording
        if (isRecording) {
            console.log('Stopping recording');
            stopRecognition();
        } else {
            console.log('Starting recording for subject', subjectId);
            startRecognition(subjectId);
        }
    });
}
// FUNCTION FOR ATTACH SPEECH HANDLERS

// FUNCTION FOR STARTING MIC RECOGNITION
function startRecognition(subjectId) {
    console.log('startRecognition called for subject', subjectId);
    
    if (!window.SpeechRecognition && !window.webkitSpeechRecognition) {
        alert('Speech recognition is not supported in your browser. Please use Chrome or Edge.');
        return;
    }
    
    // Always stop any existing recognition first
    stopRecognition();
    
    // Find UI elements
    const micAnimation = $(`#micAnimation_${subjectId}`);
    const micButton = $(`#micButton_${subjectId}`);
    
    if (!micAnimation.length || !micButton.length) {
        console.error('Could not find mic animation or button elements');
        return;
    }
    
    // Create a new recognition instance
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const recognition = new SpeechRecognition();
    
    // Configure recognition
    recognition.lang = 'en-US';
    recognition.continuous = false;
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;
    
    try {
        recognition.timeout = 10000;
    } catch (e) {
        console.log('Could not set timeout property:', e);
    }
    
    // Define all event handlers BEFORE calling start()
    recognition.onstart = function() {
        console.log('Recognition started');
        isRecording = true;
        
        // IMPORTANT: Only show animation AFTER recognition has started
        console.log('Showing mic animation');
        micAnimation.removeClass('hidden');
        micButton.text('🎤');
    };
    
    recognition.onend = function() {
        console.log('Recognition ended naturally');
        micAnimation.addClass('hidden');
        micButton.text('🔈');
        isRecording = false;
    };
    
    // Other event handlers remain the same...
    recognition.onaudiostart = function() {
        console.log('Audio capturing started');
    };
    
    recognition.onaudioend = function() {
        console.log('Audio capturing ended');
    };
    
    recognition.onsoundstart = function() {
        console.log('Some sound detected');
    };
    
    recognition.onsoundend = function() {
        console.log('Sound has stopped being detected');
    };
    
    recognition.onspeechstart = function() {
        console.log('Speech detected');
    };
    
    recognition.onspeechend = function() {
        console.log('Speech has stopped being detected');
    };
    
    recognition.onerror = function(event) {
        console.error('Recognition error:', event.error);
        
        // Hide animation and reset button on error
        micAnimation.addClass('hidden');
        micButton.text('🔈');
        isRecording = false;
        
        // Error handling code remains the same...
        let errorMessage = 'There was a problem with the speech recognition: ' + event.error;
        
        if (event.error === 'no-speech') {
            errorMessage = 'No speech was detected. Please try speaking again.';
        } else if (event.error === 'audio-capture') {
            errorMessage = 'No microphone was found or microphone is disabled.';
        } else if (event.error === 'not-allowed') {
            errorMessage = 'Microphone permission was denied. Please allow microphone access.';
        } else if (event.error === 'network') {
            errorMessage = 'Network error occurred. Please check your internet connection.';
        } else if (event.error === 'aborted') {
            errorMessage = 'Recognition was aborted.';
        }
        
        Swal.fire({
            title: 'Error!',
            text: errorMessage,
            icon: 'error'
        });
    };
    
   // Complete the recognition.onresult handler 
// Replace the existing onresult handler in startRecognition function
recognition.onresult = function(event) {
    console.log('Got result:', event.results);
    
    if (!event.results || !event.results[0]) {
        console.error('No results in the event object');
        return;
    }
    
    const transcript = event.results[0][0].transcript.toLowerCase().trim();
    const wordElement = $(`#dynamicWord_${subjectId}`);

    if (!wordElement.length) {
        console.error('Word element not found');
        return;
    }

    const expectedWord = wordElement.text().toLowerCase().trim();
    const wordId = wordElement.data('word-id');
    const confidence = event.results[0][0].confidence;

    // Show a processing indicator
    const processingToast = Swal.fire({
        title: 'Processing...',
        text: 'Evaluating your pronunciation',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Send data to backend for evaluation
    $.ajax({
        url: 'php-functions/pronunciation_evaluate.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            transcript: transcript,
            expectedWord: expectedWord,
            confidence: confidence,
            wordId: wordId
        }),
        success: function(response) {
            // Close the processing indicator
            processingToast.close();
            
            if (!response.success) {
                console.error('Error evaluating pronunciation:', response.error);
                Swal.fire({
                    title: 'Error!',
                    text: 'There was a problem evaluating your pronunciation.',
                    icon: 'error'
                });
                return;
            }
            
            const finalScore = response.finalScore;
            const pronunciationScore = response.pronunciationScore;
            const vowelMatchScore = response.vowelMatchScore || 0;
            
            // console.log('Transcript:', transcript);
            // console.log('Expected word:', expectedWord);
            // console.log('Confidence:', confidence * 100);
            // console.log('Adjusted confidence:', response.adjustedConfidence);
            // console.log('Pronunciation score:', pronunciationScore);
            // console.log('Pronunciation weight:', response.pronunciationWeight);
            // console.log('Final score:', finalScore);
            
            // Only update progress if score is 80 or above
            if (finalScore >= 80) {
                console.log('Score is 80 or above, updating progress');
                updateProgress(wordId, finalScore);
            } else {
                console.log('Score below 80, progress not updated');
            }
            
            // Get the current subject data
            const currentSubject = subjectData[subjectId];

            if (finalScore >= 80) {
                // Check if this was the last word
                if (currentSubject && currentSubject.currentIndex === currentSubject.words.length - 1) {
                    // Create phonetic breakdown
                    const phoneticElement = $(`#phoneticTranscription_${subjectId}`);
                    const phoneticText = phoneticElement.length ? phoneticElement.text().trim().split('🔊')[0].trim() : '';
                    
                    // Determine any specific feedback to provide
                    let feedbackMessage = response.feedbackMessage || '';
                    
                    Swal.fire({
                        title: finalScore >= 90 ? '🌟 Excellent!' : '👍 Good Job!',
                        html: `
                            <div class="pronunciation-feedback">
                                <div class="mb-3">
                                    <span class="font-bold">You said:</span> "${highlightDifferences(transcript, expectedWord)}"<br>
                                    <span class="font-bold">Expected:</span> "${expectedWord}"
                                </div>
                                <div class="mb-3">
                                    <span class="font-bold">Your score:</span> ${finalScore}%
                                </div>
                                ${phoneticText ? `<div class="mb-3">
                                    <span class="font-bold">Sounds like:</span> ${phoneticText}
                                </div>` : ''}
                                ${feedbackMessage ? `<div class="mt-2"><span class="text-yellow-600">${feedbackMessage}</span></div>` : ''}
                                <div class="mt-3 text-sm text-gray-600">
                                    ${finalScore >= 90 ? 
                                      (pronunciationScore < 100 ? 'Great job! Keep practicing to perfect your pronunciation!' : 'Perfect pronunciation!') : 
                                      'Keep practicing to improve your pronunciation. You\'re getting closer!'}
                                </div>
                                <div class="mt-4 pt-3 border-t border-gray-200">
                                    <p class="font-bold text-green-600">🎉 Congratulations! You've completed all words in this section!</p>
                                </div>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Review Your Work',
                        showClass: {
                            popup: 'animate__animated animate__fadeInUp animate__faster'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Load completed words view
                            loadCompletedWords(subjectId);
                        }
                    });
                }  else {
                    // Show regular success message with enhanced feedback
                    
                    // Create phonetic breakdown
                    const phoneticElement = $(`#phoneticTranscription_${subjectId}`);
                    const phoneticText = phoneticElement.length ? phoneticElement.text().trim().split('🔊')[0].trim() : '';
                    
                    // Determine any specific feedback to provide
                    let feedbackMessage = response.feedbackMessage || '';
                    
                    if (pronunciationScore === 0) {
                        Swal.fire({
                            title: '❌ Incorrect!',
                            html: `
                                <div class="pronunciation-feedback">
                                    <div class="mb-3">
                                        <span class="font-bold">You said:</span> "${highlightDifferences(transcript, expectedWord)}"<br>
                                        <span class="font-bold">Expected:</span> "${expectedWord}"
                                    </div>
                                    <div class="mb-3">
                                        <span class="font-bold">Your score:</span> 0%
                                    </div>
                                    <div class="mt-3 text-sm text-gray-600">
                                        The word you said is completely different. Please try again.
                                    </div>
                                </div>
                            `,
                            icon: 'error',
                            confirmButtonText: 'Try Again',
                            showClass: {
                                popup: 'animate__animated animate__fadeInUp animate__faster'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: finalScore >= 90 ? '🌟 Excellent!' : '👍 Good Job!',
                            html: `
                                <div class="pronunciation-feedback">
                                    <div class="mb-3">
                                        <span class="font-bold">You said:</span> "${highlightDifferences(transcript, expectedWord)}"<br>
                                        <span class="font-bold">Expected:</span> "${expectedWord}"
                                    </div>
                                    <div class="mb-3">
                                        <span class="font-bold">Your score:</span> ${finalScore}%
                                    </div>
                                    ${phoneticText ? `<div class="mb-3">
                                        <span class="font-bold">Sounds like:</span> ${phoneticText}
                                    </div>` : ''}
                                    ${feedbackMessage ? `<div class="mt-2"><span class="text-yellow-600">${feedbackMessage}</span></div>` : ''}
                                    <div class="mt-3 text-sm text-gray-600">
                                        ${finalScore >= 90 ? 
                                          (pronunciationScore < 100 ? 'Great job! Keep practicing to perfect your pronunciation!' : 'Perfect pronunciation!') : 
                                          'Keep practicing to improve your pronunciation. You\'re getting closer!'}
                                    </div>
                                </div>
                            `,
                            icon: finalScore >= 90 ? 'success' : 'info',
                            confirmButtonText: 'Next Word',
                            showClass: {
                                popup: 'animate__animated animate__fadeInUp animate__faster'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show next word
                                showNextWord(subjectId);
                            }
                        });
                    }
                }
            } else {
                // Show retry message for scores below 80%
                Swal.fire({
                    title: '🎯 Almost There!',
                    html: `
                        <div class="pronunciation-feedback">
                            <div class="mb-3">
                                <span class="font-bold">You said:</span> "${highlightDifferences(transcript, expectedWord)}"<br>
                                <span class="font-bold">Expected:</span> "${expectedWord}"
                            </div>
                            <div class="mb-3">
                                <span class="font-bold">Your score:</span> ${finalScore}%
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                Let's try this word again! Focus on your pronunciation.
                            </div>
                            ${response.feedbackMessage ? `<div class="mt-2"><span class="text-yellow-600">${response.feedbackMessage}</span></div>` : ''}
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Try Again'
                });
            }
        },
        error: function(xhr, status, error) {
            // Close the processing indicator
            processingToast.close();
            
            console.error('Error sending pronunciation data:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to evaluate pronunciation. Please try again.',
                icon: 'error'
            });
        }
    });
};
    
   // Try to start the recognition with error handling
   try {
    micButton.text('⏳'); 
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(function(stream) {
            // Permission granted, now start recognition
            stream.getTracks().forEach(track => track.stop());
            
            try {
               
                recognition.start();
                recognitionInstance = recognition;
                console.log('Recognition.start() called');
            } catch (error) {
                console.error('Error starting recognition:', error);
                micAnimation.addClass('hidden');
                micButton.text('🔈');
                isRecording = false;
                
                Swal.fire({
                    title: 'Error!',
                    text: 'Could not start speech recognition: ' + error.message,
                    icon: 'error'
                });
            }
        })
        .catch(function(err) {
            console.error('Error getting microphone permission:', err);
            micAnimation.addClass('hidden');
            micButton.text('🔈');
            isRecording = false;
            
            Swal.fire({
                title: 'Microphone Access Denied',
                text: 'You need to allow microphone access for speech recognition to work.',
                icon: 'error'
            });
        });
} catch (error) {
    console.error('Error during media permission request:', error);
    micAnimation.addClass('hidden');
    micButton.text('🔈');
    isRecording = false;
}
}
// FUNCTION FOR STARTING MIC RECOGNITION

// FUNCTION TO HIGHLIGHT LETTERS OF RESULT
function highlightDifferences(spoken, expected) {
    // Handle empty inputs
    if (!spoken || !expected) return spoken;
    
    // If they're exactly the same, return with all green
    if (spoken === expected) {
        return `<span class="text-green-600">${spoken}</span>`;
    }
    
    // For very different length words
    if (spoken.length < 2 || expected.length < 2) {
        return `<span class="text-red-600">${spoken}</span>`;
    }
    
    // Split into character arrays for comparison
    let spokenChars = spoken.split('');
    let expectedChars = expected.split('');
    
    // Prepare the result as an array of character objects with their status
    let resultArray = spokenChars.map((char, index) => {
        return { 
            char: char, 
            status: 'incorrect' // Default to incorrect
        };
    });
    
    // Step 1: Find common prefix
    let commonPrefixLength = 0;
    for (let i = 0; i < Math.min(spokenChars.length, expectedChars.length); i++) {
        if (spokenChars[i] === expectedChars[i]) {
            resultArray[i].status = 'correct';
            commonPrefixLength++;
        } else {
            break;
        }
    }
    
    // Step 2: Find common suffix (if there's no overlap with prefix)
    if (commonPrefixLength < spokenChars.length) {
        let suffixLength = 0;
        for (let i = 1; i <= Math.min(spokenChars.length - commonPrefixLength, expectedChars.length - commonPrefixLength); i++) {
            if (spokenChars[spokenChars.length - i] === expectedChars[expectedChars.length - i]) {
                resultArray[spokenChars.length - i].status = 'correct';
                suffixLength++;
            } else {
                break;
            }
        }
    }
    
    // Convert the result array to HTML with appropriate colors
    let html = '';
    let currentStatus = null;
    let currentSequence = '';
    
    resultArray.forEach((item, index) => {
        if (item.status !== currentStatus) {
            // Close previous span if it exists
            if (currentStatus !== null) {
                html += `<span class="${currentStatus === 'correct' ? 'text-green-600' : 'text-red-600'}">${currentSequence}</span>`;
                currentSequence = '';
            }
            currentStatus = item.status;
        }
        currentSequence += item.char;
    });
    
    // Add the final span
    if (currentSequence) {
        html += `<span class="${currentStatus === 'correct' ? 'text-green-600' : 'text-red-600'}">${currentSequence}</span>`;
    }
    
    return html;
}
// FUNCTION TO HIGHLIGHT LETTERS OF RESULT

// FUNCTION TO STOP MIC RECOGNITION
function stopRecognition() {
    console.log('stopRecognition called, current instance:', !!recognitionInstance);
    
    try {
        if (recognitionInstance) {
            // First set isRecording to false to prevent event handling issues
            isRecording = false;
            
            // Clean up the instance
            recognitionInstance.stop();
            recognitionInstance.onstart = null;
            recognitionInstance.onend = null;
            recognitionInstance.onresult = null;
            recognitionInstance.onerror = null;
            recognitionInstance = null;
            
            console.log('Recognition stopped successfully');
        }
    } catch (error) {
        console.error('Error stopping speech recognition:', error);
    } finally {
        // Always make sure we reset the state
        isRecording = false;
        recognitionInstance = null;
        
        // Reset all UI elements
        $('.mic-animation').addClass('hidden');
        $('.micButton').text('🔈');
        
        console.log('Recognition state reset');
    }
}
// FUNCTION TO STOP MIC RECOGNITION

// FUNCTION FOR SOUNDEX ALGORITHM
function soundex(word) {
    if (!word || typeof word !== 'string') return '';
    
    // Convert to uppercase and keep only letters
    word = word.toUpperCase().replace(/[^A-Z]/g, '');
    
    if (!word) return '';
    
    // Keep first letter
    const firstLetter = word[0];
    
    const mapping = {
        'B': 1, 'F': 1, 'P': 1, 'V': 1,
        'C': 2, 'G': 2, 'J': 2, 'K': 2, 'Q': 2, 'S': 2, 'X': 2, 'Z': 2,
        'D': 3, 'T': 3,
        'L': 4,
        'M': 5, 'N': 5,
        'R': 6
    };
    
    // Replace consonants with digits
    let code = word
        .slice(1)
        .replace(/[AEIOUHWY]/g, '') 
        .split('')
        .map(c => mapping[c] || '')
        .join('');
    
    // Remove adjacent duplicates
    code = code.charAt(0) + code.slice(1).split('').filter((c, i, arr) => c !== arr[i-1]).join('');
    
    // Ensure the result is exactly 3 digits (plus the first letter)
    return (firstLetter + code + '000').slice(0, 4);
}
// FUNCTION FOR SOUNDEX ALGORITHM


// FUNCTION FOR PRONOUNCIATION EVALUATION
function evaluatePronunciation(spokenText, targetWord) {
    // Handle empty inputs
    if (!spokenText || !targetWord) {
        // console.error('Empty inputs for pronunciation evaluation');
        return 0;
    }
    
    // Clean and normalize both inputs
    const spoken = spokenText.toLowerCase()
        .trim()
        .replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, '') // Remove punctuation
        .replace(/\s{2,}/g, ' '); // Remove extra spaces
    
    const target = targetWord.toLowerCase()
        .trim()
        .replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, '')
        .replace(/\s{2,}/g, ' ');
    
    // Log inputs for debugging
    // console.log('Cleaned inputs:', { spoken, target });
    
    // Initialize scoring variables
    let score = 0;
    const maxScore = 100;
    
    // Check for exact match first (fastest path)
    if (spoken === target) {
        // console.log('Exact match detected');
        return maxScore;
    }
    
    // Use soundex for phonetic matching of consonants
    const spokenSoundex = soundex(spoken);
    const targetSoundex = soundex(target);
    
    // console.log('Soundex comparison:', { spokenSoundex, targetSoundex });
    
    // CRITICAL: If soundex codes are completely different, apply a heavy penalty
    // This catches cases where someone says a totally different word
    if (spokenSoundex.charAt(0) !== targetSoundex.charAt(0)) {
        console.log('Completely different word detected (different first letter in Soundex)');
        return 0;
    }
    
    // Extract vowels from both words to compare them properly
    const targetVowels = target.match(/[aeiou]/g) || [];
    const spokenVowels = spoken.match(/[aeiou]/g) || [];
    
    // console.log('Vowel comparison:', { targetVowels, spokenVowels });
    
    // Modified to account for position and relative count
    let vowelMatchScore = 0;
    if (targetVowels.length > 0 && spokenVowels.length > 0) {
        // Check difference in vowel count
        const vowelCountDiff = Math.abs(targetVowels.length - spokenVowels.length);
        const vowelCountPenalty = vowelCountDiff * 10; // 10% penalty per extra/missing vowel
        
        // Count matching vowels (in order)
        let matchingVowels = 0;
        const minVowels = Math.min(targetVowels.length, spokenVowels.length);
        
        for (let i = 0; i < minVowels; i++) {
            if (targetVowels[i] === spokenVowels[i]) {
                matchingVowels++;
            }
        }
        
        // Calculate base score from matching vowels
        const baseVowelScore = Math.round((matchingVowels / targetVowels.length) * 100);
        
        // Apply penalty for different vowel counts
        vowelMatchScore = Math.max(0, baseVowelScore - vowelCountPenalty);
        // console.log('Vowel match score:', vowelMatchScore, 'matching vowels:', matchingVowels, 'vowel count penalty:', vowelCountPenalty);
    }
    
    // If soundex codes match, check if vowels also match well
    if (spokenSoundex === targetSoundex) {
        // console.log('Soundex match detected, checking vowels');
        
        // For words with different vowel patterns despite matching Soundex,
        // we need to be more strict (e.g., "chair" vs "choir")
        if (vowelMatchScore < 70) {
            const adjustedScore = Math.round(vowelMatchScore * 0.7 + 30); // Maximum 79% for bad vowel matching
            // console.log('Vowel mismatch on Soundex match, adjusted score:', adjustedScore);
            return adjustedScore;
        }
        
        // Good vowel match and matching Soundex - high score
        // console.log('Good vowel match on Soundex match');
        return 95; // High but not perfect
    }

    
    
    // For non-exact soundex match, check how similar they are
    let soundexSimilarity = 0;
    for (let i = 0; i < 4; i++) {
        if (spokenSoundex.charAt(i) === targetSoundex.charAt(i)) {
            soundexSimilarity += 25; // Each matching position is worth 25%
        }
    }
    // console.log('Soundex similarity:', soundexSimilarity + '%');
    
    // Check for close matches (like "cats" for "cat")
    if (spoken.startsWith(target) || target.startsWith(spoken)) {
        const lengthDiff = Math.abs(spoken.length - target.length);
        if (lengthDiff <= 2) { // Allow small differences like plural forms
            const closeMatchScore = Math.max(85, maxScore - (lengthDiff * 5));
            // console.log('Close prefix match detected, score:', closeMatchScore);
            return closeMatchScore;
        }
    }
    
    // For single-word targets, see if the spoken text contains the target word
    if (!target.includes(' ') && spoken.includes(target)) {
        // console.log('Target word contained in spoken text');
        return 90; // High score but not perfect
    }
    
    // Use Levenshtein distance for more complex comparison
    const distance = levenshteinDistance(spoken, target);
    const maxLength = Math.max(spoken.length, target.length);
    
    // Calculate basic score based on edit distance
    const levenshteinScore = Math.max(0, Math.round((1 - distance / maxLength) * 100));
    // console.log('Levenshtein score:', levenshteinScore);
    
    // Combine all signals: soundex similarity (40%), vowel match (30%), edit distance (30%)
    score = Math.round(
        (soundexSimilarity * 0.4) + 
        (vowelMatchScore * 0.3) + 
        (levenshteinScore * 0.3)
    );
    
    // console.log('Combined raw score:', score);
    
    // Adjust score based on word length - short words should have stricter matching
    if (target.length <= 3) {
        // For very short words, be more strict
        score = Math.round(score * 0.9); // 10% penalty for short words
        // console.log('Applied short word penalty, new score:', score);
    } else if (target.length >= 8) {
        // For longer words or phrases, be more lenient
        score = Math.min(100, Math.round(score * 1.1)); // 10% bonus for long words
        // console.log('Applied long word bonus, new score:', score);
    }
    
    // console.log('Final pronunciation score:', score);
    return score;
}
 // FUNCTION FOR PRONOUNCIATION EVALUATION

    
// FUNCTION FOR LEVENSHTEIN DISTANCE CALCULATION
    function levenshteinDistance(a, b) {
        const matrix = [];
        
        for (let i = 0; i <= b.length; i++) {
            matrix[i] = [i];
        }
        
        for (let j = 0; j <= a.length; j++) {
            matrix[0][j] = j;
        }
        
        for (let i = 1; i <= b.length; i++) {
            for (let j = 1; j <= a.length; j++) {
                if (b.charAt(i - 1) === a.charAt(j - 1)) {
                    matrix[i][j] = matrix[i - 1][j - 1];
                } else {
                    matrix[i][j] = Math.min(
                        matrix[i - 1][j - 1] + 1,
                        matrix[i][j - 1] + 1,
                        matrix[i - 1][j] + 1
                    );
                }
            }
        }
        
        return matrix[b.length][a.length];
    }
// FUNCTION FOR LEVENSHTEIN DISTANCE CALCULATION
    
// FUNCTION TO UPDATE THE PROGRESS IN DATABASE
function updateProgress(wordId, score) {
        $.ajax({
            url: 'php-functions/update_progress.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                word_id: wordId,
                score: score,
                card_id: cardId,
                completed_at: new Date().toISOString()
            }),
            success: function(data) {
                if (data.success) {
                    // Update the progress bar if we have subject ID
                    if (data.subject_id) {
                        updateProgressUI(data.subject_id, data.progress);
                    }
                } else {
                    console.error('Error updating progress:', data.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error sending progress update:', error);
            }
        });
    }
// FUNCTION TO UPDATE THE PROGRESS IN DATABASE
    
// FUNCTION TO SPEAK THE TEXT
    function speakText(text) {
        if (!text) {
            console.error('Empty text to speak');
            return;
        }
        
        // Cancel any existing speech
        window.speechSynthesis.cancel();
        
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-US';
        utterance.rate = 0.9;  // Slightly slower for clarity
        
        window.speechSynthesis.speak(utterance);
    }
// FUNCTION TO SPEAK THE TEXT
    
// HELPER FUNCTION FOR HTML ESCAPE CHARACTER
    function htmlEscape(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
});
// HELPER FUNCTION FOR HTML ESCAPE CHARACTER