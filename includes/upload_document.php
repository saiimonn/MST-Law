<?php
session_start();
require_once("db_connection.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log function for debugging
function logError($message) {
    error_log("[Upload Error] " . $message);
}

if (!isset($_SESSION['user_id']) || !isset($_POST['appointment_id'])) {
    logError("Missing user_id or appointment_id");
    header('Location: ../clientPages/documents.php?error=invalid');
    exit;
}

try {
    // Verify appointment ownership
    $query = "SELECT id FROM appointments WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_POST['appointment_id'], $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        throw new Exception('invalid');
    }

    // Check if file was uploaded
    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        logError("File upload error: " . $_FILES['document']['error']);
        throw new Exception('upload');
    }

    $file = $_FILES['document'];
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Create upload directory if it doesn't exist
    $upload_dir = __DIR__ . '/../uploads/appointment_documents/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            logError("Failed to create directory: " . $upload_dir);
            throw new Exception('upload');
        }
    }

    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    logError("File MIME type: " . $mime_type); // Debug log

    if (!in_array($mime_type, $allowed_types)) {
        logError("Invalid file type: " . $mime_type);
        throw new Exception('type');
    }

    // Validate file size
    if ($file['size'] > $max_size) {
        logError("File too large: " . $file['size']);
        throw new Exception('size');
    }

    // Generate unique filename
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $new_filename;

    // Check directory permissions
    if (!is_writable($upload_dir)) {
        logError("Directory not writable: " . $upload_dir);
        throw new Exception('upload');
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        logError("Failed to move uploaded file. Error: " . error_get_last()['message']);
        throw new Exception('upload');
    }

    // Save to database
    $query = "INSERT INTO appointment_documents (appointment_id, file_name, original_filename) 
              VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt->execute([$_POST['appointment_id'], $new_filename, $file['name']])) {
        logError("Database error: " . implode(", ", $stmt->errorInfo()));
        throw new Exception('upload');
    }

    header('Location: ../clientPages/documents.php?success=1');
    exit;

} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    header('Location: ../clientPages/documents.php?error=' . $e->getMessage());
    exit;
}