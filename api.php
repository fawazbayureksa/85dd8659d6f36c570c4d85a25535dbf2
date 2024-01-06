<?php

require_once realpath(__DIR__ . "/vendor/autoload.php");

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load environment variables from .env file in the project root
$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();


// Enable error reporting for debugging (remove for production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Function to send an email
function sendEmail($to, $subject, $message)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = getenv('MAIL_HOST'); // Your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('MAIL_USERNAME'); // Your SMTP username
        $mail->Password   = getenv('MAIL_PASSWORD'); // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port       = getenv('MAIL_PORT'); // TCP port to connect to

        //Recipients
        $mail->setFrom('your_email@example.com', 'Your Name');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
    echo $to;
}


// Simple REST API endpoint to send an email
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['to']) && isset($data['subject']) && isset($data['message'])) {
        $to = $data['to'];
        $subject = $data['subject'];
        $message = $data['message'];

        if (sendEmail($to, $subject, $message)) {
            echo json_encode(['success' => true, 'message' => 'Email sent successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send email']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
