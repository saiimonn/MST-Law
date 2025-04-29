<?php 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_id'])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../logins/dbcon.php");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// TODAY
$sqlToday = "SELECT COUNT(*) FROM tasks WHERE lawyer_id = ? AND DATE(deadline) = ? AND status != 'completed'";
if (!$stmtToday = $conn->prepare($sqlToday)) {
    die("Today prepare failed: " . $conn->error);
}
$stmtToday->bind_param("is", $user_id, $today);
$stmtToday->execute();
$stmtToday->bind_result($todayCount);
$stmtToday->fetch();
$stmtToday->close();

// UPCOMING
$sqlUpcoming = "SELECT COUNT(*) FROM tasks WHERE lawyer_id = ? AND DATE(deadline) > ? AND status != 'completed'";
if (!$stmtUpcoming = $conn->prepare($sqlUpcoming)) {
    die("Upcoming prepare failed: " . $conn->error);
}
$stmtUpcoming->bind_param("is", $user_id, $today);
$stmtUpcoming->execute();
$stmtUpcoming->bind_result($upcomingCount);
$stmtUpcoming->fetch();
$stmtUpcoming->close();

// OVERDUE
$sqlOverdue = "SELECT COUNT(*) FROM tasks WHERE lawyer_id = ? AND DATE(deadline) < ? AND status != 'completed'";
if (!$stmtOverdue = $conn->prepare($sqlOverdue)) {
    die("Overdue prepare failed: " . $conn->error);
}
$stmtOverdue->bind_param("is", $user_id, $today);
$stmtOverdue->execute();
$stmtOverdue->bind_result($overdueCount);
$stmtOverdue->fetch();
$stmtOverdue->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MST LAW</title>
    <link rel = "stylesheet" href = "../css/output.css">
</head>
<body class="flex flex-col min-h-screen bg-white py-4 px-2 sm:px-6 md:px-12 lg:px-32" style="font-family: 'Inter', sans-serif;">
    <div class="self-start mb-2">
        <a href="../lawyerPages/lawyerHome.php" class="flex items-center text-black hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
             </svg>
            Back to Dashboard
        </a>
    </div>
    <div class="w-full flex flex-col text-center mb-8">
        <h1 class="font-bold text-2xl sm:text-3xl md:text-4xl">Legal Task Manager</h1>
        <p class="text-gray-700 mt-4">Organize your schedule and never miss a deadline</p>
    </div>

    <!-- Header Cards -->
    <div class="w-full flex flex-col sm:flex-row gap-4 sm:gap-6">
        <div class="w-full sm:w-1/3 bg-white rounded-lg shadow-sm p-4 sm:p-6 border-1 border-gray-300 flex items-center gap-3">
            <div class="rounded-full bg-blue-100 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                  </svg>                  
            </div>

            <div>
                <p class="text-sm text-gray-500">Today</p>
                <p class="text-xl font-semibold"><?php echo $todayCount; ?> tasks</p>
            </div>
        </div>

        <div class="w-full md:w-1/3 bg-white rounded-lg shadow-sm p-6 border-1 border-gray-300 flex items-center gap-3">
            <div class="rounded-full bg-orange-100 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                  </svg>                  
            </div>

            <div>
                <p class="text-sm text-gray-500">Upcoming</p>
                <p class="text-xl font-semibold"><?php echo $upcomingCount; ?> tasks</p>
            </div>
        </div>

        <div class="w-full md:w-1/3 bg-white rounded-lg shadow-sm p-6 border-1 border-gray-300 flex items-center gap-3">
            <div class="rounded-full bg-red-100 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                  </svg>                  
            </div>

            <div>
                <p class="text-sm text-gray-500">Overdue</p>
                <p class="text-xl font-semibold"><?php echo $overdueCount; ?> tasks</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="rounded-lg border border-gray-200 bg-white shadow mt-4">
        <div class="p-4 sm:p-6">
            <form class="space-y-4" method="post" action="../includes/createTask.php">
                <div>
                    <input name="title" required type="text" class="flex h-10 w-full rounded-md border border-gray-300 bg-background px-3 py-2 text-sm" placeholder="Add a new task" value>
                </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <textarea name="details" class="flex min-h-[80px] w-full rounded-md border border-gray-300 bg-inherit px-3 py-2 text-sm ring-offset-inherit" placeholder="Add details (optional)"></textarea>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button id="deadlineBtn" class="inline-flex items-center gap-2 whitespace-nowrap rounded-md text-sm ring-offset-inherit transition-colors focus-visible:outline-none focus-visible:ring-2 border border-gray-300 bg-inherit h-10 px-4 py-2 w-full justify-start text-left font-normal cursor-pointer hover:bg-gray-200" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            <span>Set Deadline</span>                              
                        </button>
                        <div id="calendarContainer" class="hidden">
                            <input type="date" name="deadline" class="flex h-10 w-full rounded-md border border-gray-300 bg-inherit px-3 py-2 text-sm mb-2" min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-inherit transition-colors focus-visible:outline-none focus-visible:ring-2 bg-black text-white h-8 px-4 py-2 w-full hover:bg-gray-800 cursor-pointer" type="submit">Add Task</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="border-t border-gray-300">
            <div class="w-full" dir="ltr">
                <div class="px-2 sm:px-4 pt-3">
                    <div class="h-10 items-center justify-center rounded-md p-1 grid w-full grid-cols-3 outline-none bg-gray-100">
                        <button onclick="showAllTasks()" id="allTasks" class="px-4 py-1 rounded-md font-medium text-gray-600 whitespace-nowrap cursor-pointer">All Tasks</button>
                        <button onclick="showActive()" id="active" class="px-4 py-1 rounded-md font-medium text-gray-600 whitespace-nowrap cursor-pointer">Active</button>
                        <button onclick="showCompleted()" id="completed" class="px-4 py-1 rounded-md font-medium text-gray-600 whitespace-nowrap cursor-pointer">Completed</button>
                    </div>
                </div>

                <div id="taskContainer" class="mt-2 ring-offset-inherit">

                </div>

                <!-- <div class="mt-2 ring-offset-inherit focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 p-0">
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="rounded-full bg-gray-100 p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                              </svg>                              
                        </div>
                        <h3 class="mt-2 text-s font-medium text-gray-900">No tasks</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started</p>
                    </div>
                </div> -->
            </div>
        </div>
     </div>
</body>
<script src="../js/todoPage.js"></script>
</html>