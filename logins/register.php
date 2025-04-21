<?php 
session_start(); //manages user date
require_once "config.php"; // connects to the database using config.php

$name = $email = $password = $confirmPassword = $phone = $gender = "";
$nameError = $emailError = $passwordError = $confirmPasswordError = $phoneError = $genderError = ""; 

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $confirmPassword = trim($_POST["confirmPassword"] ?? '');
    $phone = trim($_POST["phone"] ?? '');
    $gender = trim($_POST["gender"] ?? '');
    $role = "client";

    $errors = [];

    if(empty($name)) { //Error if name is empty
        $nameError = "Please enter your name";
        $errors[] = $nameError;
    }

    if(empty($email)) { //Error if email is empty
        $emailError = "Please enter your email";
        $errors[] = $emailError;
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) { //Error if email format is invalid
        $emailError = "Email is invalid";
        $errors[] = $emailError;
    } else {
        $emailPattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if(!preg_match($emailPattern, $email)) {
            $emailError = "Invalid email format.";
            $errors[] = $emailError;
        }
    }

    if(empty($password)) { //Error if password is empty
        $passwordError = "Please enter your password";
        $errors[] = $passwordError;
    } elseif(strlen($password) < 6) { //Error if password is less than 6 chars
        $passwordError = "Password must be at least 6 characters";
        $errors[] = $passwordError;
    }

    if(empty($confirmPassword)) { //Error if confirmPass is empty
        $confirmPasswordError = "Please confirm your password";
        $errors[] = $confirmPasswordError;
    } elseif($password !== $confirmPassword) { //Error if confirmPass and password are !=
        $confirmPasswordError = "Passwords do not match";
        $errors[] = $confirmPasswordError;
    }

    if(empty($phone)) {
        $phoneError = "Please enter your phone number";
        $errors[] = $phoneError;
    } elseif(strlen($phone) < 10) {
        $phoneError = "Phone number must be at least 10 digits";
        $errors[] = $phoneError;
    }

    // Gender validation
    if(empty($gender)) {
        $genderError = "Please select your gender";
        $errors[] = $genderError;
    }

    if(empty($errors)) { // IF there are not validation errors
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); //encrypts password in database

        $sql = "INSERT INTO users (name, email, password, role, phone, gender) VALUES (?, ?, ?, ?, ?, ?)"; //statement to insert the values placed by the new user to the database

        if($stmt = $conn->prepare($sql)) { //prepares the sql statement
            $stmt->bind_param("ssssss", $name, $email, $hashed_password, $role, $phone, $gender); // binds form data to sql statement

            if($stmt->execute()) { // if the sql statement is done executing
                header("Location: login.php"); //redirect to login page
                exit;
            } else {
                $errors[] = "Something went wrong. Try again";
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
    <title>Register</title>

    <link rel="stylesheet" href="../css/form.css">
    <link rel = "stylesheet" href = "../css/style.css">
<meta name="robots" content="noindex, follow">
</head>
<body>

<header>
    <nav class = "right">
        <a href="../index.html">Back</a>
    </nav>
</header>

<div class="main">
    <!-- Sign up form -->
    <section class="signup">
        <div class="container">
            <div class="signup-content">
                <div class="signup-form">
                    <h2 class="form-title">Sign up</h2>
                    <form action = "register.php" method = "post" class="register-form" id="register-form">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" placeholder="Your Name" autocomplete="off" value="<?php echo htmlspecialchars($name ?? ''); ?>"/>
                            <span class="error"><?php echo $nameError; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" placeholder="Your Email" autocomplete="off" value="<?php echo htmlspecialchars($email ?? ''); ?>"/>
                            <span class="error"><?php echo $emailError; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="pass">Password</label>
                            <input type="password" name="password" id="pass" placeholder="Password" autocomplete="new-password"/>
                            <span class="error"><?php echo $passwordError; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="re-pass">Confirm Password</label>
                            <input type="password" name="confirmPassword" id="re_pass" placeholder="Repeat your password"/>
                            <span class="error"><?php echo $confirmPasswordError; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" name="phone" id="phone" placeholder="Your Phone Number" value="<?php echo htmlspecialchars($phone ?? ''); ?>"/>
                            <span class="error"><?php echo $phoneError; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender" class="form-select">
                                <option value="" disabled selected>Select your gender</option>
                                <option value="male" <?php echo (isset($gender) && $gender == 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo (isset($gender) && $gender == 'female') ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo (isset($gender) && $gender == 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                            <span class="error"><?php echo $genderError; ?></span>
                        </div>
                        <div class="form-group form-button">
                            <input type="submit" name="signup" id="signup" class="form-submit" value="Register"/>
                        </div>
                    </form>
                </div>
                <div class="signup-image">
                    <figure><img src="../pics/ladyJustice.png" alt="sign up image"></figure>
                    <a href="login.php" class="signup-image-link no-hover">Already have an account?</a>
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