## Send an Email using Api

### Features
1. Send an Email

### Prerequisites
-PHP version 8.1 or higher
-Composer for managing dependencies
-Mail SMTP Server (Already available)
-RabbitMQ for message queue (Already available)
-Database server (MySQL or similar) for storing email data (File send_email.sql Already available)

### Setup

**Clone Repository**
```
git clone https://github.com/fawazbayureksa/85dd8659d6f36c570c4d85a25535dbf2.git
```
### Installation

**Composer Installation**
```
composer install
```

### Setup Environment Variables
 Edit the .env file to include your database configuration

 ### Dependencies
 -phpmailer/phpmailer
 -vlucas/phpdotenv
 -php-amqplib/php-amqplib

 ## API DOCUMENTATION

### Endpoints
 url : {localhost}api.php

### PUBLIC KEY
-value : e172dd95f4feb21412a692e73929961e

### Request
-Headers
 **API_PUBLIC_KEY**: Public API key for authentication & authorization
 *if using postman in Authorization choose Type Api Key and key is API_PUBLIC_KEY and value is PUBLIC_KEY and choose Add to Query Params*
-Body
 - Content-Type: **application/json**
 ```
 {
  "to": "recipient@example.com",
  "subject": "Email Subject",
  "message": "Email Content"
 }
 ```
### Response
-Success
 -Status Code: 200
 -Content-Type: **application/json**
 ```
 {
  "success": true,
  "message": "Email sent successfully"
}
 ```
-Failure
 -Status Code: 401 (Unauthorized) or 400 (Bad Request)
 -Content-Type: **application/json**
 ```
 {
  "success": false,
  "message": "Unauthorized" or "Invalid request data" or "Invalid request method" or "Failed to send email"
}
 ```

### How to run
After create request send email using url ...api.php, to send email to all recipients run the following command:
```
php consumer.php
```

### Structure Code
-**api.php**: Main API endpoint for handling email requests.
-**producer.php**: Handles storing email data in the database and publishing messages to the message queue.
-**consume.php**: Consumes messages from the message queue, decodes JSON, and sends emails using PHPMailer.
-**db.php**: MManages database connections and stores email data.

### Notes
in .env if MQ_NAME **test** can't use,  change to **mail_queue**

