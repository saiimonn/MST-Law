<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Security check - only allow admin or authenticated users
if (!isset($_SESSION['user_id'])) {
    die('Access denied');
}

// Define the upload directory path
$uploadDir = __DIR__ . '/../uploads/';

echo "<h2>Directory Information</h2>";
echo "<pre>";
echo "Upload Directory Path: " . $uploadDir . "\n";
echo "Directory exists: " . (is_dir($uploadDir) ? 'Yes' : 'No') . "\n";
echo "Directory permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "\n";
echo "\nDirectory Contents:\n";
echo "=====================================\n";

if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $filepath = $uploadDir . $file;
            echo "\nFile: " . $file . "\n";
            echo "Size: " . filesize($filepath) . " bytes\n";
            echo "Permissions: " . substr(sprintf('%o', fileperms($filepath)), -4) . "\n";
            echo "Readable: " . (is_readable($filepath) ? 'Yes' : 'No') . "\n";
            echo "Mime Type: " . mime_content_type($filepath) . "\n";
            echo "-------------------------------------\n";
        }
    }
}
echo "</pre>";
