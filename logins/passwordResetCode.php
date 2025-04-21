<?php 
session_start();
include('dbcon.php');

// Fix the path to vendor directory
if (!file_exists('../vendor/autoload.php')) {
    die("Please install PHPMailer using composer first");
}

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_password_reset($getName, $getEmail, $token) {
    $mail = new PHPMailer(true);

    try {
        // Encrypt parameters
        $encrypted_email = base64_encode($getEmail);
        $encrypted_token = base64_encode($token);

        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;    // Disable debug output
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gementizasgg08@gmail.com';
        $mail->Password   = 'dttlrklnzeabsmoq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('gementizasgg08@gmail.com', 'Password Reset');
        $mail->addAddress($getEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Link';

        // Update the email body to include a form
        $mail->Body = "
            <h2>Hello $getName</h2>
            <h3>You requested a password reset</h3>
            <p>Click the button below to reset your password:</p>
            <form method='POST' action='http://localhost/FinalProject/logins/changePassword.php' style='display:inline;'>
                <input type='hidden' name='token' value='$token'>
                <input type='hidden' name='email' value='$getEmail'>
                <button type='submit' style='background-color: #252627; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Reset Password</button>
            </form>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

if (isset($_POST['passwordResetLink'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists
    $sql = "SELECT name, email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $token = bin2hex(random_bytes(16));

        // Update token in database
        $update_sql = "UPDATE users SET token = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $token, $email);

        if ($update_stmt->execute() && send_password_reset($row['name'], $row['email'], $token)) {
            $_SESSION['status'] = "Password reset link sent to your email";
        } else {
            $_SESSION['status'] = "Could not send reset link. Please try again.";
        }
    } else {
        $_SESSION['status'] = "No account found with that email";
    }
    
    header('Location: forgotPassword.php');
    exit();
}
?>