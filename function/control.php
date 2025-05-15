<?php
function redirectUser($user) {
    if($user['user_type'] == 'student') {
        header("Location: student/dashboardselect.php?username=" . urlencode($user['username']) . "&user_type=" . urlencode($user['user_type']));
        exit();
    } elseif($user['user_type'] == 'admin') {
        header("Location: admin/admin.php");
        exit();
    }
}

function validateLoginInput($username, $password) {
    return !empty($username) && !empty($password);
}

