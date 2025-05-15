<?php
// Start the session
session_start();

require_once('../function/dbconnect.php');

// Retrieve the logged-in user's ID from the session
$userId = $_SESSION['user_id'] ?? 0;

// Check if the user ID is valid
if ($userId <= 0) {
    header("Location: ../loginsignup_page.php");
    exit();
}

// SQL query to fetch user data including total login time
$sql = "SELECT username, profile_image, created_at, user_type, total_login_time FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Initialize totalLoginTimeFormatted with a default value
$totalLoginTimeFormatted = '0 hours 0 minutes';

// Check if user exists
if ($user) {
    $username = htmlspecialchars($user['username']);
    $userProfileImage = $user['profile_image'] ? '../' . htmlspecialchars($user['profile_image']) : 'https://via.placeholder.com/150';
    $createdAt = htmlspecialchars($user['created_at']);
    $userType = htmlspecialchars($user['user_type']);
    $totalLoginTimeSeconds = (int) $user['total_login_time'];

    // Convert seconds to hours and minutes
    $totalLoginHours = floor($totalLoginTimeSeconds / 3600);
    $totalLoginMinutes = floor(($totalLoginTimeSeconds % 3600) / 60);
    $totalLoginTimeFormatted = sprintf('%d hours %d minutes', $totalLoginHours, $totalLoginMinutes);

    // Fetch word progress details
    $sqlUserProgress = "SELECT COUNT(*) AS user_progress FROM word_progress WHERE user_id = ?";
    $stmtUserProgress = $conn->prepare($sqlUserProgress);
    $stmtUserProgress->bind_param('i', $userId);
    $stmtUserProgress->execute();
    $resultUserProgress = $stmtUserProgress->get_result();
    $userProgressCount = $resultUserProgress->fetch_assoc()['user_progress'] ?? 0;

    $sqlTotalWords = "SELECT COUNT(*) AS total_words FROM wordcard";
    $stmtTotalWords = $conn->prepare($sqlTotalWords);
    $stmtTotalWords->execute();
    $resultTotalWords = $stmtTotalWords->get_result();
    $totalWordsCount = $resultTotalWords->fetch_assoc()['total_words'] ?? 0;

    // Calculate word progress fraction
    $wordProgressFraction = $totalWordsCount > 0 ? sprintf('%d/%d', $userProgressCount, $totalWordsCount) : '0/0';

    // Fetch subject progress details
    $sqlSubjectProgress = "
    SELECT sc.subject_id,
            sc.subject_type,
           COALESCE(up.words_completed, 0) AS words_completed,
           tc.total_words,
           CONCAT(COALESCE(up.words_completed, 0), '/', tc.total_words) AS progress_fraction,
           CASE WHEN COALESCE(up.words_completed, 0) = tc.total_words THEN 1 ELSE 0 END AS is_completed
    FROM subjectcard sc
    LEFT JOIN (
        SELECT subjectcard.subject_id, COUNT(word_progress.word_id) AS words_completed
        FROM word_progress
        JOIN wordcard ON word_progress.word_id = wordcard.word_id
        JOIN subjectcard ON wordcard.subject_card_id = subjectcard.subject_id
        WHERE word_progress.user_id = ?
        GROUP BY subjectcard.subject_id
    ) up ON sc.subject_id = up.subject_id
    LEFT JOIN (
        SELECT subjectcard.subject_id, COUNT(wordcard.word_id) AS total_words
        FROM wordcard
        JOIN subjectcard ON wordcard.subject_card_id = subjectcard.subject_id
        GROUP BY subjectcard.subject_id
    ) tc ON sc.subject_id = tc.subject_id;
    ";

    $stmtSubjectProgress = $conn->prepare($sqlSubjectProgress);
    $stmtSubjectProgress->bind_param('i', $userId);
    $stmtSubjectProgress->execute();
    $resultSubjectProgress = $stmtSubjectProgress->get_result();

    // Array to hold subject progress
    $subjectProgress = [];
    while ($row = $resultSubjectProgress->fetch_assoc()) {
        $subjectProgress[] = [
            'subject_type' => $row['subject_type'],
            'progress_fraction' => $row['progress_fraction'],
            'is_completed' => $row['is_completed'] ? '✅ Completed' : '⏳ In Progress',
        ];
    }

} else {
    // Handle case when user is not found
    $username = 'Guest';
    $userProfileImage = 'https://via.placeholder.com/150';
    $createdAt = 'N/A';
    $userType = 'Unknown';
    $wordProgressFraction = '0/0'; // Default value when no data exists
    $subjectProgress = [];
}


// Save current login time
if (!isset($_SESSION['login_start_time'])) {
    $_SESSION['login_start_time'] = time(); // Set start time when user logs in
}

// When the user logs out, calculate session duration and update total login time
if (isset($_GET['logout'])) {
    if (isset($_SESSION['login_start_time'])) {
        $loginDuration = time() - $_SESSION['login_start_time']; // Calculate session duration
        $totalLoginTimeSeconds += $loginDuration;

        // Update total login time in the database
        $updateSql = "UPDATE users SET total_login_time = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param('ii', $totalLoginTimeSeconds, $userId);
        $updateStmt->execute();
        $updateStmt->close();

        unset($_SESSION['login_start_time']); // Clear session start time
    }
}

// Close the statement but NOT the connection yet
$stmt->close();

// Initialize counters for total progress
$totalCompletedSubjects = 0;
$totalSubjects = count($subjectProgress ?? []); // Total subjects with null coalescing

// Define levels and their names
$levels = [
    1 => 'Beginner',
    2 => 'Rising Star',
    3 => 'Expert',
    4 => 'Mastermind',
    5 => 'Superstar',
    6 => 'Legend'
];

// Check if $subjectProgress is an array before iterating
if (is_array($subjectProgress) && count($subjectProgress) > 0) {
    // Calculate the number of completed subjects
    foreach ($subjectProgress as $progress) {
        if (isset($progress['progress_fraction'])) {
            $fractionParts = explode('/', $progress['progress_fraction']);
            if (count($fractionParts) >= 2) { // Make sure we have both parts
                $completedWords = (int) $fractionParts[0]; // Numerator
                $totalWords = (int) $fractionParts[1]; // Denominator

                if ($completedWords === $totalWords && $totalWords > 0) {
                    $totalCompletedSubjects++;
                }
            }
        }
    }
}

// Determine the level index directly from completed subjects
// Ensure $levelIndex is always between 1 and count($levels)
$levelIndex = max(1, min($totalCompletedSubjects, count($levels)));

// Get the level name based on the calculated level index
// This is now safe because $levelIndex is guaranteed to be a valid key
$levelName = $levels[$levelIndex];

// Calculate the percentage of completed subjects
$completionPercentage = ($totalSubjects > 0) ? ($totalCompletedSubjects / $totalSubjects) * 100 : 0;

// Total number of badges
$badgeCount = 6;

// Determine how many badges to activate based on percentage
if ($completionPercentage >= 90) {
    $activeBadges = $badgeCount; // All badges active for >=90%
} elseif ($completionPercentage > 0) {
    $activeBadges = max(1, floor(($completionPercentage / 100) * $badgeCount)); // Ensure at least 1 badge for non-zero progress
} else {
    $activeBadges = 0; // No badges for 0% completion
}

// Static badges array
$badges = [
    '<i class="fas fa-star"></i>',       // Star
    '<i class="fas fa-book"></i>',       // Book
    '<i class="fas fa-pencil-alt"></i>', // Pencil
    '<i class="fas fa-file-alt"></i>',   // File
    '<i class="fas fa-graduation-cap"></i>', // Graduation cap
    '<i class="fas fa-trophy"></i>'      // Trophy
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Learning Platform</title>

    <!-- Fonts and CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: '#58CC02',
                        secondary: '#1CB0F6',
                        accent: '#FF4B4B',
                        yellow: '#FFC800',
                        purple: '#A560E8',
                    },
                    boxShadow: {
                        'sidebar': '0 0 10px rgba(0, 0, 0, 0.1)',
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        /* Profile card animations */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        /* Badge animations */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .badge-active {
            animation: pulse 2s infinite;
        }

        /* Progress bar animation */
        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .progress-bar {
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.4) 50%,
                    rgba(255, 255, 255, 0) 100%);
            animation: shimmer 2s infinite;
        }

        /* Level badge styles */
        .level-badge {
            transition: all 0.3s ease;
        }

        .level-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Level-specific colors */
        .level-1 {
            background-color: #b0bec5;
        }

        /* Light gray */
        .level-2 {
            background-color: #81d4fa;
        }

        /* Light blue */
        .level-3 {
            background-color: #4fc3f7;
        }

        /* Medium blue */
        .level-4 {
            background-color: #29b6f6;
        }

        /* Deeper blue */
        .level-5 {
            background-color: #ffa726;
        }

        /* Orange for Superstar */
        .level-6 {
            background-color: #ff7043;
        }

        /* Red for Legend */

        /* Stats card hover effect */
        .stats-card {
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        /* Badge styles */
        .badge {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .badge-active {
            background: linear-gradient(135deg, #ffe57f, #ffd740);
            border: 3px solid #ffc400;
            color: #fff;
            box-shadow: 0 5px 15px rgba(255, 196, 0, 0.3);
        }

        .badge-inactive {
            background: linear-gradient(135deg, #e0e0e0, #bdbdbd);
            border: 3px solid #9e9e9e;
            color: #333;
            opacity: 0.7;
        }

        .badge:hover {
            transform: rotate(15deg) scale(1.1);
        }

        /* Subject progress styles */
        .subject-progress-item {
            transition: all 0.3s ease;
        }

        .subject-progress-item:hover {
            transform: translateX(5px);
            background-color: #f0f9ff;
        }

        /* Fixed sidebar layout - add to any page with sidebar scrolling issues */
        .page-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar-container {
            position: fixed;
            width: 280px;
            height: 100vh;
            z-index: 40;
            overflow-y: auto; /* Allow sidebar to scroll independently if needed */
        }

        .main-container {
            flex: 1;
            margin-left: 0;
        }

        @media (min-width: 768px) {
            .main-container {
                margin-left: 280px;
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-poppins">
    <div class="page-layout">
        <!-- Sidebar Container -->
        <div class="sidebar-container">
            <?php include 'sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="main-container main-content">
            <div class="max-w-6xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">My Profile</h1>
                    <p class="text-gray-600 mt-2">View and manage your learning progress</p>
                </div>

                <!-- Profile Overview -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-32 md:h-48"></div>
                    <div class="px-6 py-8 md:px-10 md:py-8 relative">
                        <!-- Profile Image -->
                        <div class="absolute -top-16 left-10 float-animation">
                            <div
                                class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                                <img src="<?php echo $userProfileImage; ?>" alt="Profile Image"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>

                        <div class="mt-12 md:mt-16 md:flex md:justify-between md:items-end">
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                                    <?php echo htmlspecialchars($username); ?>
                                </h2>
                                <div class="flex items-center mt-2 text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Joined: <?php echo htmlspecialchars($createdAt); ?></span>
                                </div>
                            </div>

                            <div class="mt-4 md:mt-0">
                                <div
                                    class="level-badge level-<?php echo $levelIndex; ?> text-white px-4 py-2 rounded-xl font-semibold flex items-center shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                    Level <?php echo $levelIndex; ?>: <?php echo htmlspecialchars($levelName); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Learning Progress Card -->
                    <div class="stats-card bg-white rounded-2xl shadow-lg p-6 overflow-hidden relative">
                        <div class="absolute top-0 right-0 w-32 h-32 -mt-10 -mr-10 bg-blue-100 rounded-full opacity-50">
                        </div>
                        <div class="relative">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Learning Progress
                            </h3>

                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-600">Overall Completion</span>
                                    <span
                                        class="text-sm font-medium text-blue-600"><?php echo round($completionPercentage); ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="progress-bar bg-blue-500 h-3 rounded-full"
                                        style="width: <?php echo round($completionPercentage); ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-600">Words Mastered</span>
                                    <span
                                        class="text-sm font-medium text-green-600"><?php echo htmlspecialchars($wordProgressFraction); ?></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <?php
                                    $wordParts = explode('/', $wordProgressFraction);
                                    $wordPercentage = (count($wordParts) >= 2 && (int) $wordParts[1] > 0)
                                        ? ((int) $wordParts[0] / (int) $wordParts[1]) * 100
                                        : 0;
                                    ?>
                                    <div class="progress-bar bg-green-500 h-3 rounded-full"
                                        style="width: <?php echo round($wordPercentage); ?>%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-600">Learning Time</span>
                                    <span
                                        class="text-sm font-medium text-purple-600"><?php echo htmlspecialchars($totalLoginTimeFormatted); ?></span>
                                </div>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Keep learning to increase your total time!
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Achievements Card -->
                    <div class="stats-card bg-white rounded-2xl shadow-lg p-6 overflow-hidden relative">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 -mt-10 -mr-10 bg-yellow-100 rounded-full opacity-50">
                        </div>
                        <div class="relative">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                Achievements
                            </h3>

                            <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-4">
                                <?php
                                // Render the badges
                                for ($i = 0; $i < $badgeCount; $i++) {
                                    if ($i < $activeBadges) {
                                        echo '<div class="badge badge-active" title="Achievement Unlocked">' . $badges[$i] . '</div>';
                                    } else {
                                        echo '<div class="badge badge-inactive" title="Achievement Locked">' . $badges[$i] . '</div>';
                                    }
                                }
                                ?>
                            </div>

                            <div class="mt-4 p-4 bg-yellow-50 rounded-xl">
                                <h4 class="font-semibold text-gray-800 mb-2">Next Achievement</h4>
                                <div class="flex items-center">
                                    <?php if ($activeBadges < $badgeCount): ?>
                                        <div class="badge badge-inactive mr-3"><?php echo $badges[$activeBadges]; ?></div>
                                        <div>
                                            <p class="text-sm text-gray-600">Complete more subjects to unlock your next
                                                achievement!</p>
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                                <?php
                                                // Calculate progress to next badge
                                                $nextBadgeThreshold = ($activeBadges + 1) * (100 / $badgeCount);
                                                $progressToNextBadge = min(100, ($completionPercentage / $nextBadgeThreshold) * 100);
                                                ?>
                                                <div class="progress-bar bg-yellow-500 h-2 rounded-full"
                                                    style="width: <?php echo round($progressToNextBadge); ?>%"></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="badge badge-active mr-3"><i class="fas fa-crown"></i></div>
                                        <p class="text-sm text-gray-600">Congratulations! You've unlocked all achievements!
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subject Progress Section -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Subject Progress
                    </h3>

                    <?php if (empty($subjectProgress)): ?>
                        <div class="text-center py-8">
                            <img src="../pics/empty-state.svg" alt="No progress" class="w-32 h-32 mx-auto mb-4 opacity-50">
                            <h4 class="text-lg font-semibold text-gray-700 mb-2">No progress data yet</h4>
                            <p class="text-gray-500 mb-4">Start learning to see your progress here!</p>
                            <a href="dashboardselect.php"
                                class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-green-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Start Learning
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php
                            $displayedSubjects = 0;
                            foreach ($subjectProgress as $progress):
                                // Check if progress_fraction exists before trying to use it
                                if (isset($progress['progress_fraction'])) {
                                    $fractionParts = explode('/', $progress['progress_fraction']);
                                    // Only proceed if we have at least 2 elements in the array
                                    if (count($fractionParts) >= 2) {
                                        $completedWords = (int) $fractionParts[0];
                                        $totalWords = (int) $fractionParts[1];

                                        // Calculate percentage
                                        $subjectPercentage = $totalWords > 0 ? ($completedWords / $totalWords) * 100 : 0;

                                        // Display only if progress > 0
                                        if ($completedWords > 0):
                                            $displayedSubjects++;
                                            ?>
                                            <div
                                                class="subject-progress-item bg-gray-50 rounded-xl p-4 border border-gray-200 hover:border-green-300 transition-all">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h4 class="font-semibold text-gray-800">
                                                        <?php echo htmlspecialchars($progress['subject_type'] ?? 'Unknown'); ?></h4>
                                                    <span
                                                        class="<?php echo $completedWords === $totalWords ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?> text-xs px-2 py-1 rounded-full">
                                                        <?php echo $completedWords === $totalWords ? '✅ Completed' : '⏳ In Progress'; ?>
                                                    </span>
                                                </div>

                                                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                                                    <div class="progress-bar <?php echo $completedWords === $totalWords ? 'bg-green-500' : 'bg-blue-500'; ?> h-2.5 rounded-full"
                                                        style="width: <?php echo round($subjectPercentage); ?>%"></div>
                                                </div>

                                                <div class="flex justify-between items-center text-sm">
                                                    <span
                                                        class="text-gray-600"><?php echo htmlspecialchars($progress['progress_fraction']); ?>
                                                        words</span>
                                                    <span class="text-gray-600"><?php echo round($subjectPercentage); ?>%</span>
                                                </div>
                                            </div>
                                        <?php
                                        endif;
                                    }
                                }
                            endforeach;

                            // If no subjects with progress were displayed
                            if ($displayedSubjects === 0):
                                ?>
                                <div class="col-span-full text-center py-6">
                                    <p class="text-gray-500">No subject progress to display yet. Start learning to see your
                                        progress!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Learning Tips -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 -mt-10 -mr-10 bg-primary rounded-full opacity-10">
                    </div>
                    <div class="relative">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-primary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            Learning Tips
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                                <h4 class="font-semibold text-gray-800 mb-2">Daily Practice</h4>
                                <p class="text-gray-600 text-sm">Consistent daily practice, even for just 10-15 minutes,
                                    is more effective than cramming once a week.</p>
                            </div>

                            <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                                <h4 class="font-semibold text-gray-800 mb-2">Use Flashcards</h4>
                                <p class="text-gray-600 text-sm">Create flashcards for difficult words to reinforce your
                                    memory through active recall.</p>
                            </div>

                            <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
                                <h4 class="font-semibold text-gray-800 mb-2">Track Progress</h4>
                                <p class="text-gray-600 text-sm">Regularly review your progress to stay motivated and
                                    identify areas that need more attention.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Recent Activity
                    </h3>

                    <div class="border-l-2 border-gray-200 pl-4 ml-3">
                        <div class="relative mb-6">
                            <div class="absolute -left-7 mt-1 w-4 h-4 rounded-full bg-blue-500"></div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Logged in</h4>
                                <p class="text-sm text-gray-500">Current session: Active</p>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute -left-7 mt-1 w-4 h-4 rounded-full bg-gray-300"></div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Account Created</h4>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($createdAt); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-gray-500 text-sm mb-8">
                    <p>Keep learning and growing! Your progress is being tracked.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle dropdown for subject progress
        function toggleDropdown() {
            const dropdown = document.getElementById('progressDropdown');
            const button = document.querySelector('.toggle-btn-inline');

            if (dropdown.style.display === 'none') {
                dropdown.style.display = 'block';
                button.innerHTML = 'Hide ▲';
            } else {
                dropdown.style.display = 'none';
                button.innerHTML = 'Show ▼';
            }
        }

        // Confirm logout
        function confirmLogout(event) {
            event.preventDefault(); // Prevent default link behavior

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to log out?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log out!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php'; // Redirect to logout script
                }
            });
        }

        // Add animation to badges
        document.addEventListener('DOMContentLoaded', function () {
            // Add hover effect to badges
            const badges = document.querySelectorAll('.badge');
            badges.forEach(badge => {
                badge.addEventListener('mouseenter', () => {
                    if (badge.classList.contains('badge-active')) {
                        const audio = new Audio('../sounds/pop.mp3');
                        audio.volume = 0.2;
                        audio.play();
                    }
                });
            });

            // Add confetti effect for completed subjects
            const completedSubjects = document.querySelectorAll('.bg-green-100');
            completedSubjects.forEach(subject => {
                subject.addEventListener('mouseenter', () => {
                    createConfetti(subject);
                });
            });
        });

        // Confetti effect function
        function createConfetti(element) {
            const colors = ['#FFD700', '#FF69B4', '#00CED1', '#98FB98'];
            for (let i = 0; i < 5; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'absolute w-2 h-2 rounded-full pointer-events-none';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.top = Math.random() * 100 + '%';
                confetti.style.setProperty('--tx', (Math.random() * 200 - 100) + 'px');
                confetti.style.setProperty('--ty', (Math.random() * -100 - 50) + 'px');
                confetti.style.setProperty('--r', (Math.random() * 360) + 'deg');
                confetti.style.animation = 'confetti 1s ease-out forwards';

                element.appendChild(confetti);
                setTimeout(() => confetti.remove(), 1000);
            }
        }
    </script>
</body>

</html>
<?php
// Close the connection AFTER including the sidebar
$conn->close();
?>