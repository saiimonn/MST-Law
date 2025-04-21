<?php
session_start();
include('../logins/dbcon.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $attorney_id = $_POST['attorney'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $phone = $_POST['phone'];
    $description = $_POST['description'];
    $documents = [];

    // Handle file uploads
    if (!empty($_FILES['documents']['name'][0])) {
        $upload_dir = '../uploads/';
        foreach ($_FILES['documents']['name'] as $key => $name) {
            $tmp_name = $_FILES['documents']['tmp_name'][$key];
            $file_name = time() . '_' . basename($name);
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($tmp_name, $file_path)) {
                $documents[] = $file_name;
            }
        }
    }

    $documents_json = json_encode($documents);

    $sql = "INSERT INTO appointments (user_id, attorney_id, date, start_time, end_time, phone, description, documents) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssss", $user_id, $attorney_id, $date, $start_time, $end_time, $phone, $description, $documents_json);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment request submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to submit appointment request."]);
    }
}
?>