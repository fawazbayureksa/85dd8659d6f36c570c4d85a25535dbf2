<?php

require_once realpath(__DIR__ . "/vendor/autoload.php");
include 'producer.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

// Function to send an email
function sendEmail($to, $subject, $message)
{
    try {
        storeMessage($to, $subject, $message);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// if there is request 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $apiKey = getenv('PUBLIC_API_KEY');

    $requestApiKey = $_GET['API_PUBLIC_KEY'] ?? '';

    // Authorized
    if ($requestApiKey !== $apiKey) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
    // =========

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
