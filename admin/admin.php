<?php
session_start();
require_once '../function/dbconnect.php';

// Check if user is logged in and is admin
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
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<style>
    .print-only-stats {
        display: none;
    }

    @media print {
        .print-only-stats {
            display: block !important;
            margin-bottom: 20px;
            page-break-before: auto;
        }

        /* --- NEW RULES --- */
        /* Force 3 columns for the grid inside the stats section */
        .print-only-stats .grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 1rem !important;
            margin-bottom: 20px !important; /* Adjust gap as needed for print */    
        }
        .print-only-stats .grid > div { /* Target direct div children of the grid */
             -webkit-print-color-adjust: exact !important; /* Use !important for higher priority */
             print-color-adjust: exact !important;
        }

     .chart-print{
        margin-top: 100px;
     }

        #reportContent {
            display: block !important;
        }


        .dashboard-header,
        aside,
        .print-button-container {
            display: none !important;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;     
        }

    }
</style>

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
        <main class="flex-1 p-8 bg-gray-50">
            <div class="max-w-7xl mx-auto" id="reportContent">
                <!-- Header Section -->
                <header class="dashboard-header">
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                    <p class="welcome-text text-xl mt-2">Welcome back, Admin</p>
                </header>

                <!-- Print Button -->
                <div class="flex justify-end mb-4 print-button-container items-center">
    <div class="mr-3">
        <select id="reportTimePeriod" class="bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:shadow-outline">
            <option value="all">All Time</option>
            <option value="day">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
        </select>
    </div>
    <!-- Update the onclick handler -->
    <button onclick="printReportWithStats()" class="bg-[#7056a1] hover:bg-[#5a4581] text-white font-bold py-2 px-4 rounded inline-flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        <span>Print Report</span>
    </button>
</div>


                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 main-dashboard-grid">
                    
                    <div class="dashboard-card p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p id="total-users" class="text-sm font-medium text-gray-600">Total Users</p>
                                <h3 id="total-users-count" class="text-2xl font-bold text-gray-800 mt-2">0</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                      <!-- Total Subject Cards -->
                      <div class="dashboard-card p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p id="total-subject-cards" class="text-sm font-medium text-gray-600">Total Subject Cards</p>
                                <h3 id="total-subject-cards-count" class="text-2xl font-bold text-gray-800 mt-2">0</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <!-- Total Words -->
                    <div class="dashboard-card p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p id="total-words" class="text-sm font-medium text-gray-600">Total Words</p>
                                <h3 id="total-words-count" class="text-2xl font-bold text-gray-800 mt-2">0</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Average Rating -->
                    <div class="dashboard-card p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p id="average-rating" class="text-sm font-medium text-gray-600">Average Rating</p>
                                <h3 id="average-progress-count" class="text-2xl font-bold text-gray-800 mt-2">0</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 chart-print">
                    <div class="dashboard-card p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">User Activity</h3>
                        <canvas id="loginTimeChart" height="200"></canvas>
                    </div>
                    <div class="dashboard-card p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Learning Progress</h3>
                        <canvas id="learningProgressChart" height="200"></canvas>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script src="javascript-handler/admin.js"></script>
    <script src="javascript-handler/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="javascript-handler/stats.js"></script>
    <script>
        

        // Tab switching functionality
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = tab.dataset.tab;

                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });

                document.getElementById(targetId).classList.remove('hidden');

                document.querySelectorAll('.sidebar-link').forEach(t => {
                    t.classList.remove('bg-white/10');
                });
                tab.classList.add('bg-white/10');
            });
        });
    </script>
</body>

</html>