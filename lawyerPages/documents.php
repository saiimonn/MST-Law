<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION["user_name"])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../includes/db_connection.php");

$query = "SELECT a.id, a.date as appointment_date, a.description, u.name as client_name,
          ad.id as document_id, ad.file_name, ad.original_filename, ad.uploaded_at
          FROM appointments a
          LEFT JOIN appointment_documents ad ON a.id = ad.appointment_id
          JOIN users u ON a.user_id = u.id
          WHERE a.attorney_id = ? AND a.status = 'confirmed'
          ORDER BY a.date DESC, ad.uploaded_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$appointments = [];
foreach($results as $row) {
    $appointmentId = $row['id'];
    if(!isset($appointments[$appointmentId])) {
        $appointments[$appointmentId] = [
            'id' => $row['id'],
            'appointment_date' =>$row['appointment_date'],
            'client_name' => $row['client_name'],
            'description' => $row['description'],
            'documents' => []
        ];
    }
    if($row['document_id']) {
        $appointments[$appointmentId]['documents'][] = [
            'id' => $row['document_id'],
            'file_name' => $row['file_name'],
            'original_filename' => $row['original_filename'],
            'uploaded_at' => $row['uploaded_at']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents</title>
    <link rel = "stylesheet" href = "../css/home.css">
    <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel = "stylesheet" href = "../css/documents.css">
</head>
<body>

    <?php include_once("../includedFiles/header.php")?>
    
    <main class = "dashboard">
        <header>
            <h3>Documents</h3>
        </header>

        <div class = "documents-container">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    switch($_GET['error']) {
                        case 'type':
                            echo "Invalid file type. Please upload a PDF file.";
                            break;
                        case 'size':
                            echo "File is too large. Maximum size is 5MB.";
                            break;
                        case 'upload':
                            echo "Error uploading file. Please try again.";
                            break;
                        default:
                        echo "An error occurred.";
                    }
                    ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['success'])): ?>
                <div class = "alert alert-success">Document uploaded successfully!</div>
            <?php endif; ?>

            <?php if(empty($appointments)):?>
                <p>No confirmed appointments found.</p>
            <?php else: ?>
                <?php foreach($appointments as $appointment): ?>
                    <div class = "appointment-card">
                        <div class = "appointment-details">
                            <p><strong>Appointment ID: </strong> <?php echo htmlspecialchars($appointment['id']); ?></p>
                            <p><strong>Client: </strong> <?php echo htmlspecialchars($appointment['client_name']); ?></p>
                            <p><strong>Date: </strong> <?php echo htmlspecialchars(date('F j, Y', strtotime($appointment['appointment_date']))); ?></p>
                            <p><strong>Description: </strong> <?php echo htmlspecialchars($appointment['description']); ?></p>
                        </div>
                        <div class = "documents-list">
                            <div class = "documents-header">
                                <h5>Documents</h5>
                            </div>
                            
                            <?php if(!empty($appointment['documents'])): ?>
                                <?php foreach($appointment['documents'] as $document): ?>
                                    <div class="document-item">
                                        <div>
                                            <span><?php echo htmlspecialchars($document['original_filename'] ?? $document['file_name']); ?></span>
                                            <small>(Uploaded: <?php echo date('M j, Y', strtotime($document['uploaded_at'])); ?>)</small>
                                        </div>
                                        <a href="../includes/download.php?id=<?php echo htmlspecialchars($document['id']); ?>" class="download-btn" download>
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-documents">No documents uploaded yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src = "../js/menuToggle.js"></script>
</body>
</html>