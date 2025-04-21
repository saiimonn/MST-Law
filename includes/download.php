<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session and buffer
ob_start();
session_start();
require_once("db_connection.php");

// Debug information
error_log("Download request started for ID: " . ($_GET['id'] ?? 'none'));

// Check if user is logged in and document ID is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    error_log("Session user_id or GET id missing");
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

try {
    // Fetch document details and verify ownership
    $query = "SELECT ad.*, a.user_id, a.attorney_id 
              FROM appointment_documents ad
              JOIN appointments a ON ad.appointment_id = a.id
              WHERE ad.id = ? AND (a.user_id = ? OR a.attorney_id = ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$_GET['id'], $_SESSION['user_id'], $_SESSION['user_id']]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) {
        error_log("Document not found or access denied for user: " . $_SESSION['user_id']);
        throw new Exception('Document not found or access denied');
    }

    // Handle both absolute and relative paths
    $filepath = $document['file_path'];
    error_log("Original file path: " . $filepath);

    if (strpos($filepath, '..') === 0) {
        // Relative path starting with ../
        $filepath = __DIR__ . '/' . $filepath;
    } elseif (!str_starts_with($filepath, '/')) {
        // Just filename, check in appointment_documents directory
        $filepath = __DIR__ . '/../uploads/appointment_documents/' . $filepath;
    }
    // else use the absolute path as is

    error_log("Resolved filepath: " . $filepath);
    
    if (!file_exists($filepath)) {
        // Try alternate locations
        $possible_paths = [
            __DIR__ . '/../uploads/appointment_documents/' . basename($filepath),
            __DIR__ . '/../uploads/documents/' . basename($filepath),
            __DIR__ . '/../uploads/' . basename($filepath)
        ];

        foreach ($possible_paths as $path) {
            error_log("Trying path: " . $path);
            if (file_exists($path)) {
                $filepath = $path;
                break;
            }
        }
    }

    if (!is_file($filepath) || !is_readable($filepath)) {
        error_log("Final file path not accessible: " . $filepath);
        throw new Exception('File not accessible on server');
    }

    // Get file extension and set mime type
    $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    
    // Set appropriate mime type
    if ($extension === 'pdf') {
        $mime_type = 'application/pdf';
    } elseif (in_array($extension, ['jpg', 'jpeg'])) {
        $mime_type = 'image/jpeg';
    } else {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $filepath);
        finfo_close($finfo);
    }

    error_log("File mime type: " . $mime_type);

    // Clear any output buffers and turn off output buffering
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Set headers for download
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . basename($document['original_filename']) . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: public');

    // Output file
    if (!readfile($filepath)) {
        throw new Exception('Failed to read file');
    }
    exit;

} catch (Exception $e) {
    error_log('Download error: ' . $e->getMessage());
    header('Location: ../clientPages/documents.php?error=download');
    exit;
}