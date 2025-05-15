<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../loginsignup_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cards - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Include your sidebar here -->
        <aside class="w-64 admin-gradient text-white">
            <div class="p-8">
                <div class="flex items-center space-x-3 mb-10">
                    <div class="p-2 bg-white/10 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-300">Admin Panel</span>
                </div>
                <nav class="space-y-4">
                    <a href="admin.php" class="sidebar-link flex items-center space-x-4 group" data-tab="dashboard">
                        <div class="p-2 rounded-lg bg-white/10 group-hover:bg-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                            </svg>
                        </div>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="manage_card.php" class="sidebar-link flex items-center space-x-4 group">
                        <div class="p-2 rounded-lg bg-white/10 group-hover:bg-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="font-medium">Manage Cards</span>
                    </a>
                    <!-- <a href="#" class="sidebar-link flex items-center space-x-4 group" data-tab="users">
                        <div class="p-2 rounded-lg bg-white/10 group-hover:bg-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span class="font-medium">Users</span>
                    </a> -->
                    <a href="php-functions/logout.php" class="sidebar-link flex items-center space-x-4 group text-red-200 hover:text-red-100 hover:bg-red-500">
                        <div class="p-2 rounded-lg bg-white/10 group-hover:bg-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <span class="font-medium">Logout</span>
                    </a>
                </nav>
            </div>
        </aside>
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <div class="dashboard-header mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Manage Learning Cards</h1>
                    <p class="text-gray-600 mt-2">Create and manage learning cards for students</p>
                </div>

                <!-- Add New Card Form -->
                <div class="dashboard-card p-6 mb-8">
                    <h2 class="text-xl font-semibold mb-4">Add New Learning Card</h2>
                    <form id="learning-card" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Card Title</label>
                                <input type="text" name="title"
                                    class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                                <select name="level"
                                    class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500">
                                    <option value="Beginner">Beginner</option>
                                    <option value="Intermediate">Intermediate</option>
                                    <option value="Advanced">Advanced</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Card Image</label>
                                <input type="file" name="card_image"
                                    class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500"
                                    accept="image/*">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select name="category"
                                    class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500">
                                    <option value="Vocabulary">Vocabulary</option>
                                    <option value="Sentence">Sentence</option>
                                    <option value="Pronunciation">Pronunciation</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="4"
                                class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
                            Create Card
                        </button>
                    </form>
                </div>


                <!-- Existing Cards Grid -->
                <div class="dashboard-card p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Existing Learning Cards</h2>
        <div class="flex space-x-4">
            <select id="categoryFilter"
                class="px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500">
                <option value="">All Categories</option>
                <option value="Vocabulary">Vocabulary</option>
                <option value="Sentence">Sentence</option>
                <option value="Pronunciation">Pronunciation</option>
                <option value="Conversation">Conversation</option>
            </select>
        </div>
    </div>
    <!-- Cards will be dynamically inserted here -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    </div>
</div>

                <!-- Pagination -->
                <div class="flex justify-center mt-8">
                    <nav class="flex space-x-2">
                        <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">Previous</button>
                        <button class="px-4 py-2 border rounded-lg bg-indigo-600 text-white">1</button>
                        <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">2</button>
                        <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">3</button>
                        <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">Next</button>
                    </nav>
                </div>
            </div>
    </div>
    </main>
    </div>

    <!-- MODAL FOR EDITING LEARNING CARD -->
<div class="modal fade" id="editCardModal" tabindex="-1" aria-labelledby="editCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCardModalLabel">Edit Learning Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCardForm" enctype="multipart/form-data">
                    <input type="hidden" id="edit_card_id" name="card_id">
                    <div class="mb-3">
                        <label for="edit_card_title" class="form-label">Card Title</label>
                        <input type="text" class="form-control" id="edit_card_title" name="card_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="edit_category" name="category" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_difficulty_level" class="form-label">Difficulty Level</label>
                        <select class="form-select" id="edit_difficulty_level" name="difficulty_level" required>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_card_image" class="form-label">Card Image</label>
                        <input type="file" class="form-control" id="edit_card_image" name="card_image">
                        <div id="current_image" class="mt-2"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCardChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('cardForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('manage_card.php', {
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

        // For delete operations
        function deleteCard(cardId) {
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
                    fetch(`manage_card.php?action=delete&card_id=${cardId}`, {
                        method: 'DELETE'
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Deleted!',
                                    data.message,
                                    'success'
                                ).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    data.message,
                                    'error'
                                );
                            }
                        });
                }
            });
        }
        // For update operations
        function updateCard(cardId) {
            window.location.href = `manage_subject_cards.php?grade_card_id=${cardId}`;
        }
        function viewCardWords(cardId) {
            fetch(`manage_card.php?action=view_words&card_id=${cardId}`)
                .then(response => response.json())
                .then(data => {
                    let wordsHtml = '';
                    if (data.words && data.words.length > 0) {
                        wordsHtml = data.words.map(word => `
                    <div class="flex justify-between items-center p-2 border-b">
                        <span class="font-medium">${word.word}</span>
                        <span class="text-gray-600">${word.phonetic || ''}</span>
                    </div>
                `).join('');
                    } else {
                        wordsHtml = '<p class="text-center text-gray-500">No words added yet</p>';
                    }

                    Swal.fire({
                        title: 'Card Words',
                        html: `<div class="max-h-96 overflow-y-auto">${wordsHtml}</div>`,
                        width: '600px',
                        confirmButtonText: 'Close'
                    });
                });
        }

    </script>

<script src="javascript-handler/manage-cards.js"></script>
</body>

</html>