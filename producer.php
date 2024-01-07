<?php

require_once realpath(__DIR__ . "/vendor/autoload.php");
require 'db.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Dotenv\Dotenv;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

function storeMessage($to, $subject, $message)
{
    $connection = new AMQPStreamConnection(getenv('MQ_HOST'), getenv('MQ_PORT'), getenv('MQ_USER'), getenv('MQ_PASS'), getenv('MQ_VHOST'));
    $channel = $connection->channel();
    // test,  queue name
    $channel->queue_declare(getenv('MQ_NAME'), false, true, false, false);

    $messageBody = [
        'to' => $to,
        'subject' => $subject,
        'message' => $message,
    ];

    // Store the message to database
    storeMessageToDB($to, $subject, $message, json_encode($messageBody));

    $message = new AMQPMessage(json_encode($messageBody));

    // Publish the message to queue
    $channel->basic_publish($message, '', getenv('MQ_NAME')); // test,  queue name

    $channel->close();
    $connection->close();
}
