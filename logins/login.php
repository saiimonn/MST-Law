<?php 
session_start();
require_once "config.php";

$emailError = $passwordError = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $errors = [];

    if(empty($email)) {
        $emailError = "Please enter your email";
        $errors[] = $emailError;
    }

    if(empty($password)) {
        $passwordError = "Please enter your password";
        $errors[] = $passwordError;
    }

    if(empty($errors)) {
        $sql = "SELECT id, name, password, role FROM users WHERE email = ?";

        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if($stmt->num_rows == 1) {
                $stmt->bind_result($id, $name, $hashed_password, $role);
                $stmt->fetch();

                if(password_verify($password, $hashed_password)) {
                    $_SESSION["user_id"] = $id;
                    $_SESSION["user_name"] = $name;
                    $_SESSION["user_role"] = $role;
                    
                    if($role == "client") {
                        header("Location: ../clientPages/clientHome.php");
                    } elseif($role == "lawyer") {
                        header("Location: ../lawyerPages/lawyerHome.php");
                    } elseif($role == "admin") {
                        header("Location: ../adminPages/adminHome.php");
                    }
                    exit;
                } else {
                    $passwordError = "Incorrect password";
                    $errors[] = $passwordError;
                }
            } else {
                $emailError = "No account found with that email";
                $errors[] = $emailError;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <!-- Main css -->
    <link rel="stylesheet" href="../css/form.css">
    <link rel = "stylesheet" href = "../css/style.css">
<meta name="robots" content="noindex, follow">
</head>
<body>

<div class="main">
    <!-- Sign in form -->
    <section class="sign-in">
        <div class="container">
            <div class="signin-content">
                <div class="signin-image">
                    <figure><img src="../pics/lawTimeIDK.png" alt="sign up image"></figure>
                    <a href="register.php" class="signup-image-link no-hover">Don't have an account?</a>
                    <a href="forgotPassword.php" class="signup-image-link no-hover">Forgot password?</a>
                    <a href="../index.html" class="signup-image-link no-hover">Back to Home</a>
                </div>

                <div class="signin-form">
                    <h2 class="form-title">Sign in</h2>
                    <?php
                    if(isset($_SESSION['login_message'])) {
                        echo '<div class="alert success">' . $_SESSION['login_message'] . '</div>';
                        unset($_SESSION['login_message']);
                    }
                    if(isset($_SESSION['status'])) {
                        $status_class = strpos($_SESSION['status'], 'success') !== false ? 'success' : 'error';
                        echo '<div class="alert ' . $status_class . '">' . $_SESSION['status'] . '</div>';
                        unset($_SESSION['status']);
                    }
                    ?>
                    <form method="POST" class="register-form" id="login-form">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" placeholder="Your Email" value="<?php echo htmlspecialchars($email ?? ''); ?>" autocomplete = "off"/>
                            <span class="error"><?php echo $emailError; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" placeholder="Password"/>
                            <span class="error"><?php echo $passwordError; ?></span>
                        </div>
                        <div class="form-group form-button">
                            <input type="submit" name="signin" id="signin" class="form-submit" value="Log in"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="js/main.js"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-23581568-13');
</script>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->
</html>