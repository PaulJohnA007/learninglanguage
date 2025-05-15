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
    <title>Manage Subject Cards - Admin Dashboard</title>
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
        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <div class="dashboard-header mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Manage Subject Cards</h1>
                </div>

                <!-- Add Subject Card Form -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl font-semibold mb-4">Add New Subject Card</h2>
                    <form id="subjectCardForm" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Subject Type</label>
                                <select name="subject_type" class="w-full rounded-lg border-gray-300" style="border: 1px solid #d1d5db;">
                                    <option value="English">English</option>
                                    <option value="Mathematics">Mathematics</option>
                                    <option value="Science">Science</option>
                                    <option value="History">History</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Title</label>
                                <input type="text" name="title" class="w-full rounded-lg focus:ring-2 focus:ring-indigo-500" style="border: 1px solid #d1d5db;" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Description</label>
                            <textarea name="description" class="w-full rounded-lg border-gray-300" style="border: 1px solid #d1d5db;"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Card Image</label>
                            <input type="file" name="card_image" accept="image/*">
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                            Create Subject Card
                        </button>
                    </form>
                </div>

                  <!-- Subject Cards Display -->
                  <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                      <div class="flex justify-between items-center mb-6">
                          <h2 class="text-xl font-semibold">Current Subject Cards</h2>
                          <div class="flex gap-2">
                              <select class="rounded-lg border-gray-300">
                                  <option value="all">All Subjects</option>
                                  <option value="english">English</option>
                                  <option value="mathematics">Mathematics</option>
                              </select>
                              <input type="search" placeholder="Search cards..." class="rounded-lg border-gray-300">
                          </div>
                      </div>

                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                      </div>
                  </div>

              </div>
        </main>
    </div>

    <!-- EDIT MODAL FOR EDITING SUBJECT CARD -->
<div class="modal fade" id="editSubjectCardModal" tabindex="-1" aria-labelledby="editSubjectCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSubjectCardModalLabel">Edit Subject Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSubjectCardForm">
                    <input type="hidden" id="edit_subject_id" name="subject_id">
                    <input type="hidden" id="edit_card_id" name="card_id">
                    
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
    <label for="edit_subject_type" class="form-label">Subject Type</label>
    <input type="text" class="form-control" id="edit_subject_type" name="subject_type" required>
</div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_subject_card_image" class="form-label">Card Image</label>
                        <input type="file" class="form-control" id="edit_subject_card_image" name="subject_card_image">
                        <div id="current_image" class="mt-2"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSubjectCardChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Example JavaScript for form submission
        document.getElementById('subjectCardForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('manage_subject_cards.php', {
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

        function manageWords(cardId) {
            window.location.href = `manage_words.php?subject_card_id=${cardId}`;
        }
    </script>
</body>
<script src="javascript-handler/manage-subject-cards.js"></script>
</html>