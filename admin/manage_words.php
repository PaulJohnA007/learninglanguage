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
    <title>Manage Words - Admin Dashboard</title>
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
        <!-- Sidebar -->
        <aside class="w-64 admin-gradient text-white">
            <div class="p-8">
                <div class="flex items-center space-x-3 mb-10">
                    <div class="p-2 bg-white/10 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span
                        class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-300">Admin
                        Panel</span>
                </div>
                <nav class="space-y-2">
                    <a href="admin.php" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="manage_card.php" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>Manage Cards</span>
                    </a>
                    <!-- <a href="#" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg" data-tab="users">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span>Users</span>
                    </a> -->
                    <a href="php-functions/logout.php"
                        class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-red-200 hover:text-red-100 hover:bg-red-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span>Logout</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <div class="dashboard-header mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Manage Words/Sentence</h1>
                    <p class="text-gray-600 mt-2">Add and manage words/sentences for subject cards</p>
                </div>

                <!-- Add Word Form -->
                <div class="dashboard-card p-6 mb-8">
                    <h2 class="text-xl font-semibold mb-4">Add New Word or Sentence</h2>
                    <form id="wordForm" class="space-y-6" enctype="multipart/form-data">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Word/Sentence</label>
            <input type="text" name="word"
                class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500"
                required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Phonetic Spelling</label>
            <input type="text" name="phonetic"
                class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Definition</label>
        <textarea name="definition" rows="3"
            class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500"></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Example Sentence</label>
        <textarea name="example" rows="2"
            class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500"></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
        <input type="file" name="word_image"
            class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500">
    </div>
    <button type="submit"
        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
        Add Word/Sentence
    </button>
</form>
                </div>

                <!-- Words List -->
                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Word/Sentence List</h2>
                        <div class="flex gap-4">
                            <select class="px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500">
                                <option value="all">All Categories</option>
                                <option value="nouns">Nouns</option>
                                <option value="verbs">Verbs</option>
                                <option value="adjectives">Adjectives</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full data-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Word/Sentence</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Phonetic</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Definition</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Example</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Example row -->
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">Example</td>
                                    <td class="px-6 py-4 whitespace-nowrap">/ɪɡˈzæmpəl/</td>
                                    <td class="px-6 py-4">A representative form or pattern</td>
                                    <td class="px-6 py-4">This is an example sentence.</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <button class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal for editing words-->
    <div class="modal fade" id="editWordModal" tabindex="-1" aria-labelledby="editWordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWordModalLabel">Edit Word</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editWordForm" enctype="multipart/form-data">
                    <input type="hidden" id="edit_word_id" name="word_id">
                    <div class="mb-3">
                        <label for="edit_word" class="form-label">Word</label>
                        <input type="text" class="form-control" id="edit_word" name="word" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_phonetic_spelling" class="form-label">Phonetic Spelling</label>
                        <input type="text" class="form-control" id="edit_phonetic_spelling" name="phonetic_spelling" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_definition" class="form-label">Definition</label>
                        <textarea class="form-control" id="edit_definition" name="definition" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_example_sentence" class="form-label">Example Sentence</label>
                        <textarea class="form-control" id="edit_example_sentence" name="example_sentence" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_word_image" class="form-label">Word Image</label>
                        <input type="file" class="form-control" id="edit_word_image" name="word_image" accept="image/*">
                        <div class="mt-3">
                            <img id="edit_word_image_preview" src="" alt="Image Preview" class="img-fluid" style="max-height: 200px; display: none;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveWordChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>

    <script src="javascript-handler/manage-word.js"></script>
    <script>
        document.getElementById('wordForm').addEventListener('submit', function (e) {
            e.preventDefault();
            // Add form submission logic here
        });
        // Add this to the existing $(document).ready function

        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('mobile-open') &&
                !sidebar.contains(e.target) &&
                !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });

        // Add these styles to your CSS


        // Insert styles into document
        const styleSheet = document.createElement('style');
        styleSheet.textContent = styles;
        document.head.appendChild(styleSheet);

    </script>
</body>

</html>