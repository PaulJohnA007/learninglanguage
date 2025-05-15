<?php
session_start();
require_once 'dbconnect.php';
require '../vendor/autoload.php'; // Make sure this path is correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if the email exists in the users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate OTP
        $otp = rand(100000, 999999);

        // Save OTP to the database
        $updateStmt = $conn->prepare("UPDATE users SET otp_code = ? WHERE email = ?");
        $updateStmt->bind_param("is", $otp, $email);
        $updateStmt->execute();

        // Send OTP to email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'learnenglishsystem@gmail.com'; // SMTP username
            $mail->Password = 'sbcx ltjb pufr fjyd'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('leanenglishsystem@gmail.com', 'English Learning');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Your OTP code is: <b>$otp</b>";

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'OTP sent to your email']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Mailer Error: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>