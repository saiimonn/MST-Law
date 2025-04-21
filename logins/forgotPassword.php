<?php 
session_start();

$email = '';
$emailError = '';

if(isset($_SESSION['user_id'])) {
    $emailError = $_SESSION['form_errors']['emaiol'] ?? '';
    $email = $_SESSION['form_data']['email'] ?? '';
    unset($_SESSION['form_errors'], $_SESSION['form_data']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel = "stylesheet" href = "../css/form.css">
    <link rel = "stylesheet" href = "../css/style.css">
</head>
<body>

<header>
    <nav class = "right">
        <a href="login.php">Back</a>
    </nav>
</header>

<section class="sign-in">
        <div class="container">
            <div class="signin-content">

                <div class="signin-form">
                    <h2 class="form-title">Forgot Password</h2>
                    <?php
                    if(isset($_SESSION['status'])) {
                        $status_class = strpos($_SESSION['status'], 'success') !== false ? 'success' : 'error';
                        echo '<div class="alert ' . $status_class . '">' . $_SESSION['status'] . '</div>';
                        unset($_SESSION['status']);
                    }
                    ?>
                    <form action = "passwordResetCode.php" method="POST" class="register-form" id="login-form">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" placeholder="Your Email" value="<?php echo htmlspecialchars($email ?? ''); ?>" autocomplete = "off"/>
                            <?php if ($emailError): ?>
                                <span class="error"><?php echo $emailError; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group form-button">
                            <input type="submit" name="passwordResetLink" id="signin" class="form-submit" value="Log in"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
</html>