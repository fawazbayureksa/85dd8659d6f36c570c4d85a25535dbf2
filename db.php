<?php
require_once realpath(__DIR__ . "/vendor/autoload.php");
date_default_timezone_set('Asia/Jakarta');

use Dotenv\Dotenv;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

function storeMessageToDB($to, $subject, $message, $body)
{

    $conn =  mysqli_connect(
        getenv('DB_HOST'),
        getenv('DB_USER'),
        getenv('DB_PASSWORD'),
        getenv('DB_NAME'),
    );

    // Check the connection
    if ($conn->connect_errno) {
        die("Failed to connect to MySQL: " . $conn->connect_error);
    }

    $id = rand(10000, 100000); // genete id
    $to = $to;
    $subject = $subject;
    $message = $message;
    $created_at = date('Y-m-d H:i:s'); // 
    $updated_at = date('Y-m-d H:i:s'); // 

    $idJob = rand(10000, 100000); // genete id
    $type  = 'send';

    $query = "INSERT INTO mail (id, receipt_email, subject, content, created_at,updated_at) VALUES ('$id', '$to', '$subject', '$message', '$created_at','$updated_at')";


    $queryJob = "INSERT INTO job_queue (id, message,type, created_at,updated_at) VALUES ('$idJob','$body','$type','$created_at','$updated_at')";

    if ($conn->query($query) && $conn->query($queryJob)) {
        return true;
    } else {
        return $conn->error;
    }
    return;
}
