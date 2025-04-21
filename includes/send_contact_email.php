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

// Prepare email
$to = 'gementizasgg08@gmail.com'; // <-- Change this to your desired recipient
$email_subject = "Contact Form: $subject";
$email_body = "You have received a new message from the contact form:\n\n"
    . "Name: $name\n"
    . "Email: $email\n"
    . "Subject: $subject\n"
    . "Message:\n$message\n";
$headers = "From: $email\r\nReply-To: $email\r\n";

// Send email
if (mail($to, $email_subject, $email_body, $headers)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
}
exit;