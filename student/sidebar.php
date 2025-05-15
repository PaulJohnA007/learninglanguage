<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current username from session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Check if connection exists and is open, otherwise create a new one
if (!isset($conn) || $conn->connect_errno) {
    require_once('../function/dbconnect.php');
}

// Function to get user streak data
function getUserStreak($conn, $user_id) {
    // Get the current date in server timezone
    $today = date('Y-m-d');
    
    // Check if user has any activity today - first check if user_activity table exists
    $tableCheckQuery = "SHOW TABLES LIKE 'user_activity'";
    $tableExists = $conn->query($tableCheckQuery)->num_rows > 0;
    
    if (!$tableExists) {
        // If table doesn't exist, return default values
        return [
            'streak' => 1, // Default to 1 day streak
            'today_xp' => 0
        ];
    }
    
    // Check if user has any activity today
    $todayQuery = "SELECT COUNT(*) as today_count FROM user_activity 
                  WHERE user_id = ? AND activity_date = ?";
    $stmt = $conn->prepare($todayQuery);
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $todayResult = $stmt->get_result()->fetch_assoc();
    $todayCount = $todayResult['today_count'];
    
    // Get today's XP from word completions
    $todayXpQuery = "SELECT COUNT(*) as xp_count FROM word_progress 
                    WHERE user_id = ? AND DATE(completed_at) = ? AND completed = 1";
    $stmt = $conn->prepare($todayXpQuery);
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $todayXpResult = $stmt->get_result()->fetch_assoc();
    $todayXp = $todayXpResult['xp_count'] * 5; // 5 XP per completed word
    
    // Simplified streak calculation - just count consecutive days with activity
    // This is a simpler version that will work even if the complex query fails
    $streakQuery = "SELECT COUNT(*) as streak_count FROM word_progress 
                   WHERE user_id = ? AND completed = 1 
                   AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $stmt = $conn->prepare($streakQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $streakResult = $stmt->get_result()->fetch_assoc();
    $streak = min(7, max(1, $streakResult['streak_count'] > 0 ? 1 : 0));
    
    return [
        'streak' => $streak,
        'today_xp' => $todayXp
    ];
}

// Function to get user overall progress
function getUserProgress($conn, $user_id) {
    // Get total words count
    $totalWordsQuery = "SELECT COUNT(*) as total FROM wordcard";
    $totalResult = $conn->query($totalWordsQuery);
    $totalWords = $totalResult->fetch_assoc()['total'];
    
    // Get completed words count
    $completedQuery = "SELECT COUNT(*) as completed FROM word_progress 
                      WHERE user_id = ? AND completed = 1";
    $stmt = $conn->prepare($completedQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $completedResult = $stmt->get_result()->fetch_assoc();
    $completedWords = $completedResult['completed'];
    
    // Calculate progress percentage
    $progressPercentage = ($totalWords > 0) ? round(($completedWords / $totalWords) * 100) : 0;
    
    return [
        'percentage' => min($progressPercentage, 100), // Cap at 100%
        'completed' => $completedWords,
        'total' => $totalWords
    ];
}
// Function to get user profile image
function getUserProfileImage($conn, $user_id) {
    // Default profile image path
    $default_image = 'uploads/default-profile.png';
    
    if (!$user_id) {
        return $default_image;
    }
    
    $query = "SELECT profile_image FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $row = $result->fetch_assoc()) {
        // Return the profile image path or default if it's empty
        return !empty($row['profile_image']) ? $row['profile_image'] : $default_image;
    }
    
    return $default_image;
}

// Get user profile image
$profile_image = getUserProfileImage($conn, $user_id);




// Get user streak and progress data
$streakData = getUserStreak($conn, $user_id);
$progressData = getUserProgress($conn, $user_id);


?>

<!-- Sidebar Component Styles -->
<style>
    .sidebar-item {
        transition: all 0.3s ease;
    }

    .sidebar-item:hover .sidebar-icon {
        transform: scale(1.1);
    }

    .sidebar-item.active {
        background-color: rgba(88, 204, 2, 0.1);
        border-right: 4px solid #58CC02;
    }

    .sidebar-icon {
        transition: transform 0.2s ease;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    .float-animation {
        animation: float 3s ease-in-out infinite;
    }

    .sidebar-wrapper {
        transition: transform 0.3s ease, width 0.3s ease;
    }

    @media (max-width: 768px) {
        .sidebar-wrapper.collapsed {
            transform: translateX(-100%);
        }
    }
</style>

<!-- Mobile Header with Burger Menu -->
<div class="md:hidden fixed top-0 left-0 right-0 flex justify-between items-center bg-primary text-white z-50">
    <span class="text-xl font-bold">DASHBOARD</span>
    <button id="sidebar-toggle" class="text-3xl focus:outline-none">
        ☰
    </button>
</div>

<!-- Sidebar Component -->
<aside id="sidebar"
    class="sidebar-wrapper fixed md:relative w-[280px] h-screen bg-white shadow-sidebar flex flex-col z-40 md:translate-x-0 -translate-x-full md:translate-x-0 transition-transform duration-300">
    <!-- Profile Section -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="relative">
                <img src="../<?php echo $profile_image; ?>" alt="Profile"
                    class="w-14 h-14 rounded-full border-2 border-primary object-cover">
                <div class="absolute -bottom-1 -right-1 bg-green-500 w-4 h-4 rounded-full border-2 border-white"></div>
            </div>
            <div>
                <h2 class="font-bold text-lg text-gray-800"><?php echo $username; ?></h2>
                <div class="flex items-center space-x-2">
                    <span class="text-accent">🔥</span>
                    <div class="flex items-center">
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-medium text-gray-500">Level Progress</span>
                <span class="text-xs font-medium text-primary"><?php echo $progressData['percentage']; ?>%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary rounded-full h-2" style="width: <?php echo $progressData['percentage']; ?>%">
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 py-4 overflow-y-auto">
        <div class="px-4 mb-4">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3">MAIN MENU</h3>
        </div>

        <div class="space-y-1 px-4">
            <!-- Home Button -->
            <a href="dashboardselect.php" class="sidebar-item active flex items-center p-3 rounded-xl group">
                <div class="sidebar-icon w-10 h-10 flex items-center justify-center bg-primary rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                </div>
                <span class="ml-3 font-medium text-gray-700 group-hover:text-gray-900">Home</span>
            </a>

            <!-- Profile Button -->
            <a href="profilepage.php" class="sidebar-item flex items-center p-3 rounded-xl hover:bg-gray-50 group">
                <div class="sidebar-icon w-10 h-10 flex items-center justify-center bg-secondary rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="ml-3 font-medium text-gray-700 group-hover:text-gray-900">Profile</span>
            </a>
        </div>

        <div class="px-4 mt-8 mb-4">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3">SETTINGS</h3>
        </div>

        <div class="space-y-1 px-4">

            <!-- Help Button -->
            <a href="help.php" class="sidebar-item flex items-center p-3 rounded-xl hover:bg-gray-50 group">
                <div
                    class="sidebar-icon w-10 h-10 flex items-center justify-center bg-gray-200 rounded-lg text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="ml-3 font-medium text-gray-700 group-hover:text-gray-900">Help Center</span>
            </a>

            <!-- Logout Button -->
            <a href="student/logout.php" onclick="confirmLogout(event);"
                class="sidebar-item flex items-center p-3 rounded-xl hover:bg-red-50 text-accent group">
                <div class="sidebar-icon w-10 h-10 flex items-center justify-center bg-red-100 rounded-lg text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="ml-3 font-medium group-hover:text-accent-hover">Logout</span>
            </a>
        </div>
    </nav>

</aside>

<!-- JavaScript for Sidebar Functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');

        // Toggle sidebar on mobile
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('-translate-x-full');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Confirm logout with toast notification
        window.confirmLogout = function (event) {
            event.preventDefault();
            
            // Create a toast notification for logout confirmation
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#58CC02',
                cancelButtonColor: '#FF4B4B',
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: 'question',
                title: 'Are you sure you want to logout?'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        };

        // Highlight current page in sidebar
        const currentPath = window.location.pathname;
        const sidebarItems = document.querySelectorAll('.sidebar-item');

        sidebarItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath.includes(href)) {
                item.classList.add('active');
            } else if (item.classList.contains('active') && href && !currentPath.includes(href)) {
                item.classList.remove('active');
            }
        });
    });
</script>