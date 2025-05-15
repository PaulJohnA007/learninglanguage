
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'englishlearn');
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
$conn = connectDB();

// define('DB_HOST', 'sql312.infinityfree.com');
// define('DB_USER', 'if0_38512289');
// define('DB_PASS', 'learning143');
// define('DB_NAME', 'if0_38512289_englishlearn');
// function connectDB() {
//     $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

//     if ($conn->connect_error) {
//         die("Connection failed: " . $conn->connect_error);
//     }
//     return $conn;
// }
// $conn = connectDB();
?>
