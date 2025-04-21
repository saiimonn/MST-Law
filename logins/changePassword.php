<?php
session_start();
include('dbcon.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get data from POST instead of GET
$token = $_POST['token'] ?? '';
$email = $_POST['email'] ?? '';
$error = '';
$success = '';

// Store token and email in session for the form submission
if ($token && $email) {
    $_SESSION['reset_token'] = $token;
    $_SESSION['reset_email'] = $email;
}

// Use stored session values
$token = $_SESSION['reset_token'] ?? '';
$email = $_SESSION['reset_email'] ?? '';

// Verify token and email
if (empty($token) || empty($email)) {
    $error = "Invalid password reset link";
} else {
    try {
        $sql = "SELECT * FROM users WHERE email = ? AND token = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error = "Invalid or expired password reset link";
        }
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            // Generate a new random token instead of empty string
            $new_token = bin2hex(random_bytes(16));
            
            $update_sql = "UPDATE users SET password = ?, token = ? WHERE email = ? AND token = ?";
            $update_stmt = $conn->prepare($update_sql);
            
            if (!$update_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $update_stmt->bind_param("ssss", $hashed_password, $new_token, $email, $token);
            
            if ($update_stmt->execute()) {
                // Clear the session variables after successful password change
                unset($_SESSION['reset_token']);
                unset($_SESSION['reset_email']);
                $_SESSION['login_message'] = "Password has been updated successfully. Please login with your new password.";
                header("Location: login.php");
                exit();
            } else {
                throw new Exception("Execute failed: " . $update_stmt->error);
            }
        } catch (Exception $e) {
            $error = "Failed to update password: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <div class="main">
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="../pics/lawTimeIDK.png" alt="password reset image"></figure>
                        <a href="login.php" class="signup-image-link no-hover">Back to login?</a>
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">Change Password</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert error"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert success"><?php echo $success; ?></div>
                        <?php else: ?>
                            <form method="POST" class="register-form">
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                                </div>
                                <div class="form-group form-button">
                                    <input type="submit" name="change_password" class="form-submit" value="Change Password">
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
