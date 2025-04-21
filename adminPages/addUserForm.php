<?php 
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../logins/dbcon.php");

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Basic validation
    if ($name && $gender && $email && $password && $role) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "A user with this email already exists.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $insert = $conn->prepare("INSERT INTO users (name, gender, email, password, role, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $insert->bind_param("sssss", $name, $gender, $email, $hashedPassword, $role);
            if ($insert->execute()) {
                $success = "User created successfully!";
            } else {
                $error = "Failed to create user. Please try again.";
            }
            $insert->close();
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a User</title>
    <link rel = "stylesheet" href = "../css/output.css">
</head>
<body class="flex flex-col bg-white px-4 py-6 sm:px-8 md:px-16 lg:px-48 xl:px-64 min-h-screen">
    <div class="w-full flex flex-col">
        <div class="w-full mb-4">
            <a href="adminHome.php" class="flex items-center text-black hover:text-gray-700 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <div class="bg-white w-full max-w-full sm:max-w-2xl md:max-w-3xl lg:max-w-4xl xl:max-w-5xl border rounded-md border-gray-400 p-4 sm:p-8 mx-auto">
            <div class="flex flex-col w-full gap-1">
                <h3 class="text-xl font-bold">Create a New User</h3>
                <p class="text-md text-gray-500">Add a new user to the system</p>
            </div>

            <?php if ($success): ?>
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800"><?php echo $success; ?></div>
            <?php elseif ($error): ?>
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="p-0 sm:p-6 pt-0 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div class="space-y-2">
                            <label for="name" class="text-sm font-medium leading-none">Full Name</label>
                            <input name="name" type="text" placeholder="John Doe" class="flex h-10 w-full rounded-md border border-gray-300 bg-inherit px-3 py-2 text-sm ring-offset-inherit" autocomplete = "off" required>
                        </div>
                        <div class="space-y-2">
                            <label for="gender" class="text-sm font-medium leading-none">Gender</label>
                            <select name="gender" class="flex h-10 w-full rounded-md border border-gray-300 bg-inherit px-3 py-2 text-sm ring-offset-inherit" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium leading-none">Email</label>
                        <input type="email" name="email" class="flex h-10 w-full rounded-md border border-gray-300 bg-inherit px-3 py-2 text-sm ring-offset-inherit" autocomplete="off" placeholder="john.doe@example.com" autocomplete = "off" required>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium leading-none">Password</label>
                        <input type="password" name="password" class="flex h-10 w-full rounded-md border border-gray-300 bg-inherit px-3 py-2 text-sm ring-offset-inherit" autocomplete="off" placeholder="*********" autocomplete = "off" required>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div class="space-y-2">
                            <label for="role" class="text-sm font-medium leading-none">User Role</label>
                            <select name="role" class="flex h-10 w-full rounded-md border border-gray-300 bg-inherit px-3 py-2 text-sm ring-offset-inherit" required>
                                <option value="client">Client</option>
                                <option value="lawyer">Lawyer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="space-y-2 flex justify-end items-end">
                            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-inherit transition-colors bg-black text-white h-10 px-4 py-2 min-w-[120px] hover:bg-black/90">Create user</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>