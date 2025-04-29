<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$fields = ['name', 'email', 'subject', 'message'];
foreach ($fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }
}

$name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$subject = filter_var($_POST['subject'], FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_var($_POST['message'], FILTER_SANITIZE_SPECIAL_CHARS);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

// Use PHPMailer via email_config.php
require_once __DIR__ . '/email_config.php';

$fullMessage = "Subject: $subject\n\n$message";

if (sendEmail($name, $email, $fullMessage)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
}
exit;