<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

header('Content-Type: application/json'); // Set JSON header
$response = [];

if (isset($_POST['email'])) {
    $recipientEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Validate the email address
    if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid email address.';
        echo json_encode($response);
        exit();
    }

    // Generate a random tenant ID
    $tenantId = strtoupper(bin2hex(random_bytes(4))); // Generates a random 8-character ID

    // Create an instance of PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dennismwendwa2718@gmail.com'; // SMTP username
        $mail->Password = 'lrxd hnlh pndo sknv'; // Use a secure way to store this
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('dennismwendwa2718@gmail.com', 'Tenant Registration');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Tenant ID';
        $mail->Body = "<h1>Your Registration is Successful!</h1><p>Your Tenant ID is: <strong>$tenantId</strong></p>";

        $mail->send();
        $response['status'] = 'success';
        $response['message'] = 'Tenant ID has been sent to your email.';
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Email address missing';
}

echo json_encode($response); // Ensure this is always called
exit();
?>
