<?php

require_once realpath(__DIR__ . "/vendor/autoload.php");

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

$connection = new AMQPStreamConnection(getenv('MQ_HOST'), getenv('MQ_PORT'), getenv('MQ_USER'), getenv('MQ_PASS'), getenv('MQ_VHOST'));
$channel = $connection->channel();

$channel->queue_declare('test', false, true, false, false);

$callback = function (AMQPMessage $message) {
    $jsonString = $message->body;
    $emailData = json_decode($jsonString, true);

    $mailer = new PHPMailer(true);

    if ($emailData !== null) {
        $to = $emailData['to'];
        $subject = $emailData['subject'];
        $messageContent = $emailData['message'];


        $mailer->isSMTP();
        $mailer->Host       = getenv('MAIL_HOST'); // Host
        $mailer->SMTPAuth   = true;
        $mailer->Username   = getenv('MAIL_USERNAME'); // SMTP username
        $mailer->Password   = getenv('MAIL_PASSWORD'); // SMTP password
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mailer->Port       = getenv('MAIL_PORT'); // port to connect to

        $mailer->setFrom(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME'));
        $mailer->addAddress($to);

        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body = $messageContent;

        if ($mailer->send()) {
            echo 'Email sent successfully' . PHP_EOL;
        } else {
            echo 'Error sending email: ' . $mailer->ErrorInfo . PHP_EOL;
        }

        // echo "Received email: To=$to, Subject=$subject, Message=$messageContent\n";
    } else {
        echo "Error decoding JSON: $jsonString\n";
    }
};

$channel->basic_consume(getenv('MQ_NAME'), '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
