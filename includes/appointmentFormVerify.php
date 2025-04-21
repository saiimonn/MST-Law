<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once("db_connection.php");

try {
    // Start transaction
    $conn->beginTransaction();

    // Check if all required fields are set
    if (empty($_POST["attorney"]) || empty($_POST["date"]) || 
        empty($_POST["start_time"]) || empty($_POST["end_time"]) || 
        empty($_POST["description"])) {
        throw new Exception("All fields are required!");
    }

    $user_id = $_SESSION['user_id'];
    $attorney_id = $_POST["attorney"];
    $date = $_POST["date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    $description = $_POST["description"];

    // Insert into appointments table first (without document info)
    $sql = "INSERT INTO appointments (user_id, attorney_id, date, 
            start_time, end_time, description, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
            
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        $user_id,
        $attorney_id,
        $date,
        $start_time,
        $end_time,
        $description
    ]);

    if ($result) {
        $appointment_id = $conn->lastInsertId();

        // Handle document upload if present
        if (!empty($_FILES['document']['name'])) {
            $uploadDir = "../uploads/documents/";
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if ($_FILES['document']['error'] === UPLOAD_ERR_OK) {
                if ($_FILES['document']['size'] > 5 * 1024 * 1024) {
                    throw new Exception("File is too large (max 5MB)");
                }

                $original_filename = $_FILES['document']['name'];
                $file_name = uniqid() . '_' . $original_filename;
                $file_path = $uploadDir . $file_name;

                if (move_uploaded_file($_FILES['document']['tmp_name'], $file_path)) {
                    // Insert into appointment_documents table
                    $docSql = "INSERT INTO appointment_documents 
                              (appointment_id, file_name, original_filename, file_path) 
                              VALUES (?, ?, ?, ?)";
                    $docStmt = $conn->prepare($docSql);
                    $docStmt->execute([
                        $appointment_id,
                        $file_name,
                        $original_filename,
                        $file_path
                    ]);
                } else {
                    throw new Exception("Failed to save file");
                }
            }
        }

        $conn->commit();
        $_SESSION['success'] = "Appointment scheduled successfully!";
    } else {
        throw new Exception("Failed to schedule appointment");
    }

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Appointment Error: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
} finally {
    header("Location: ../clientPages/newAppointment.php");
    exit();
}
?>