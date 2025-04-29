<?php 
session_start();
if(!isset($_SESSION['user_name']) || !isset($_SESSION['user_id'])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../logins/dbcon.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MST LAW</title>
    <link rel="stylesheet" href="../css/output.css">
</head>
<body class="flex flex-col min-h-screen px-2 sm:px-6 md:px-16 lg:px-52 py-4 sm:py-8" style="font-family: 'Inter', sans-serif">

    <div class="flex min-h-screen flex-col">
        <header class="sticky top-0 z-10 flex h-16 items-center gap-4 border-b px-2 sm:px-6 justify-between bg-white">
            <div class="flex items-center mb-2">
                <a href="../adminPages/adminHome.php" class="flex items-center text-black hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold">Data Analytics</h1>
            </div>
        </header>

        <main class="flex-1 overflow-auto p-2 sm:p-6">
            <!-- Buttons and Searchbar start -->
            <div dir="ltr" class="space-y-4 sm:space-y-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <div role="tablist" aria-orientation="horizontal" class="inline-flex h-10 items-center justify-center rounded-md p-1 outline-none bg-gray-200">
                        <button onclick="showOverview()" id="overviewBtn" type="button" role="tab" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-inherit transition-all bg-white">Overview</button>
                        <button onclick="showUsers()" id="usersBtn" type="button" role="tab" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-inherit transition-all">Users</button>
                    </div>
                </div>
            </div>
            <!-- Buttons and Searchbar End -->

            <!-- Overview section start -->
            <div id="overview" class="mt-2 ring-offset-inherit space-y-6">
                <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Card 1 -->
                    <div class="rounded-lg border border-gray-400 shadow-sm bg-white">
                        <div class="space-y-1.5 p-6 flex flex-row items-center justify-between pb-2">
                            <h3 class="tracking-tight text-sm font-medium">Total Users</h3>
                        </div>
                            <?php
                                    $usersSql = "SELECT COUNT(*) FROM users";
                                    $userStmt = $conn->prepare($usersSql);
                                    $userStmt->execute();
                                    $userStmt->bind_result($totalUsers);
                                    $userStmt->fetch();
                                    $userStmt->close();
                            ?>
                        <div class="p-6 pt-0">
                            <h1 class="text-2xl font-bold"><?php echo $totalUsers; ?></h1>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-400 shadow-sm bg-white">
                        <div class="space-y-1.5 p-6 flex flex-row items-center justify-between pb-2">
                            <h3 class="tracking-tight text-sm font-medium">Total Confirmed Appointments</h3>
                        </div>
                            <?php 
                                $appSql = "SELECT COUNT(*) FROM appointments
                                            WHERE status = 'confirmed'";
                                $appStmt = $conn->prepare($appSql);
                                $appStmt->execute();
                                $appStmt->bind_result($totalApp);
                                $appStmt->fetch();
                                $appStmt->close();
                            ?>
                        <div class="p-6 pt-0">
                            <h1 class="text-2xl font-bold"><?php echo $totalApp; ?></h1>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-400 shadow-sm bg-white">
                        <div class="space-y-1.5 p-6 flex flex-row items-center justify-between pb-2">
                            <h3 class="tracking-tight text-sm font-medium">Total Open Cases</h3>
                        </div>
                            <?php 
                                $caseSql = "SELECT COUNT(*) FROM cases
                                            WHERE status = 'open'";
                                $caseStmt = $conn->prepare($caseSql);
                                $caseStmt->execute();
                                $caseStmt->bind_result($totalCase);
                                $caseStmt->fetch();
                                $caseStmt->close();
                            ?>
                        <div class="p-6 pt-0">
                            <h1 class="text-2xl font-bold"><?php echo $totalCase; ?></h1>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-400 shadow-sm bg-white">
                        <div class="space-y-1.5 p-6 flex flex-row items-center justify-between pb-2">
                            <h3 class="tracking-tight text-sm font-medium">Total Active Lawyers</h3>
                        </div>
                            <?php 
                                $attySql = "SELECT COUNT(*) FROM users
                                WHERE role = 'lawyer' AND status = 'active'";
                                $attyStmt = $conn->prepare($attySql);
                                $attyStmt->execute();
                                $attyStmt->bind_result($totalActiveAtty);
                                $attyStmt->fetch();
                                $attyStmt->close();
                            ?>
                        <div class="p-6 pt-0">
                            <h1 class="text-2xl font-bold"><?php echo $totalActiveAtty; ?></h1>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-lg border border-gray-400 col-span-1 bg-white overflow-x-auto">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <h3 class="text-2xl font-semibold leading-none tracking-tight">Users By Role</h3>
                            <p class="text-sm text-gray-500">Distribution of users by their roles</p>
                        </div>
                            <?php 
                                $roleData = [];
                                $roles = ['admin', 'lawyer', 'client'];

                                foreach($roles as $role) {
                                    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
                                    $stmt->bind_param("s", $role);
                                    $stmt->execute();
                                    $stmt->bind_result($count);
                                    $stmt->fetch();
                                    $roleData[$role] = $count;
                                    $stmt->close();
                                }
                            ?>
                        <div class="p-6 pt-0 px-2">
                            <div class="h-80 flex justify-center">
                                <canvas id="usersByRoleChart" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-400 col-span-1 bg-white overflow-x-auto">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <h3 class="text-2xl font-semibold leading-none tracking-tight">Appointment Status</h3>
                            <p class="text-sm text-gray-500">Distribution of appointments by status</p>
                        </div>
                            <?php 
                                // Get appointment status data
                                $appointmentStatusData = [];
                                $statuses = ['pending', 'confirmed', 'cancelled'];
                                foreach($statuses as $status) {
                                    $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE status = ?");
                                    $stmt->bind_param("s", $status);
                                    $stmt->execute();
                                    $stmt->bind_result($count);
                                    $stmt->fetch();
                                    $appointmentStatusData[$status] = $count;
                                    $stmt->close();
                                }
                                
                                // Get case status data
                                $caseStatusData = [];
                                $caseStatuses = ['open', 'closed', 'pending'];
                                foreach($caseStatuses as $status) {
                                    $stmt = $conn->prepare("SELECT COUNT(*) FROM cases WHERE status = ?");
                                    $stmt->bind_param("s", $status);
                                    $stmt->execute();
                                    $stmt->bind_result($count);
                                    $stmt->fetch();
                                    $caseStatusData[$status] = $count;
                                    $stmt->close();
                                }
                            ?>
                        <div class="p-6 pt-0 px-2">
                            <div class="h-80 flex justify-center">
                                <canvas id="appointmentStatusByChart" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-400 col-span-1 bg-white overflow-x-auto">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <h3 class="text-2xl font-semibold leading-none tracking-tight">Case Status</h3>
                            <p class="text-sm text-gray-500">Distribution of cases by status</p>
                        </div>
                            <?php 
                                $caseStatusData = [];
                                $caseStatuses = ['open' , 'closed'];
                                
                                foreach($caseStatuses as $status) {
                                    $stmt = $conn->prepare("SELECT COUNT(*) FROM cases WHERE status = ?");
                                    $stmt->bind_param("s", $status);
                                    $stmt->execute();
                                    $stmt->bind_result($count);
                                    $stmt->fetch();
                                    $caseStatusData[$status] = $count;
                                    $stmt->close();
                                }
                            ?>
                        <div class="p-6 pt-0 px-2">
                            <div class="h-80 flex justify-center">
                                <canvas id="caseStatusChart" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <!-- Overview Section End -->

        <!-- Users section start -->
            <div id="users" class="mt-2 ring-offset-inherit space-y-6 hidden">
                <div class="grid gap-4 grid-cols-1">
                    <div class="rounded-lg border border-gray-400 shadow-sm bg-white overflow-x-auto">
                        <div class="flex flex-col p-6 space-y-1.5">
                            <h3 class="text-2xl font-semibold leading-none tracking-tight">User Status</h3>
                            <p class="text-sm">Active vs. Inactive users</p>
                        </div>
                        <?php 
                            $userStatusResult = $conn->query("SELECT status, COUNT(*) as total FROM users GROUP BY status");

                            $userStatusData = [];

                            while($row = $userStatusResult->fetch_assoc()) {
                                $userStatusData[$row['status']] = (int)$row['total'];
                            }
                        ?>
                        <div class="p-6 pt-0 px-2">
                            <div class="h-80 flex justify-center">
                                <canvas id="userStatusChart" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-400 shadow-sm">
                        <div class="flex flex-col p-6 space-y-1.5">
                            <h3 class="text-2xl font-semibold leading-none tracking-tight">Lawyer Productivity</h3>
                            <p class="text-sm">Cases per Lawyer</p>
                        </div>
                        <?php 
                            $lawyerCaseSql = "SELECT users.name AS lawyer_name, COUNT(cases.id) AS total_cases
                                            FROM cases
                                            JOIN users ON cases.lawyer_id = users.id
                                            WHERE users.role = 'lawyer'
                                            GROUP BY users.id
                                            ORDER BY total_cases DESC";

                            $lawyerCaseResult = $conn->query($lawyerCaseSql);
                            $lawyers = [];
                            $caseCounts = [];

                            while($lawyerCaseRow = $lawyerCaseResult->fetch_assoc()) {
                                $lawyers[] = $lawyerCaseRow['lawyer_name'];
                                $caseCounts[] = (int)$lawyerCaseRow['total_cases'];
                            }
                        ?>
                        <div class="p-6 pt-0 px-2">
                            <div class="h-80 flex justify-center">
                                <canvas id="lawCaseChart" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- User section end -->
        </main>
    </div>
</body>
<script>
    const usersByRoleData = <?php echo json_encode($roleData); ?>;
    const appointmentStatusData = <?php echo json_encode($appointmentStatusData); ?>;
    const caseStatusData = <?php echo json_encode($caseStatusData); ?>;
    const userStatusData = <?php echo json_encode($userStatusData); ?>;

    const lawyerNames = <?php echo json_encode($lawyers); ?>;
    const caseCounts = <?php echo json_encode($caseCounts); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/analyticScript.js"></script>
</html>