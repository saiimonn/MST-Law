<?php 
session_start();
if(!isset($_SESSION['user_name']) || !isset($_SESSION['user_id'])) {
    header("Location: ../logins/login.php");
    exit();
}
require_once("../logins/dbcon.php");

//to get the cases for displaying
$caseSql = "SELECT cases.*, users.name AS lawyer_name
            FROM cases 
            JOIN users ON cases.lawyer_id = users.id
            WHERE cases.status = 'open' OR (cases.status = 'closed' AND cases.next_hearing >= CURDATE())
            ORDER BY cases.next_hearing ASC";
$caseStmt = $conn->prepare($caseSql);
$caseStmt->execute();
$caseResult = $caseStmt->get_result();

//to get the appointments
$appointmentSql = "SELECT appointments.*, 
                          client.name AS client_name, 
                          lawyer.name AS lawyer_name
                   FROM appointments
                   JOIN users AS client ON appointments.user_id = client.id
                   JOIN users AS lawyer ON appointments.attorney_id = lawyer.id
                   WHERE appointments.status = 'confirmed' 
                   OR (appointments.status = 'pending' AND appointments.date >= CURDATE())
                   OR (appointments.status = 'cancelled' AND appointments.date >= CURDATE())
                   ORDER BY appointments.date ASC";
$appStmt = $conn->prepare($appointmentSql);
$appStmt->execute();
$appResult = $appStmt->get_result();

// for select options in the modal
$lawyerSql = "SELECT * FROM users
                WHERE role = 'lawyer'";
$lawyerStmt = $conn->prepare($lawyerSql);
$lawyerStmt->execute();
$lawyerResult = $lawyerStmt->get_result();


$clientSql = "SELECT * FROM users
                WHERE role = 'client'";
$clientStmt = $conn->prepare($clientSql);
$clientStmt->execute();
$clientResult = $clientStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MST LAW</title>
    <link rel="stylesheet" href="../css/output.css">
</head>
<body class="flex flex-col min-h-screen bg-white py-4 px-2 sm:px-6 md:px-12 lg:px-32" style="font-family: 'Inter', sans-serif;">
    <div class="flex flex-col mx-auto gap-5 w-full max-w-full md:max-w-5xl lg:max-w-7xl">
        <div class="self-start mb-2">
            <a href="../adminPages/adminHome.php" class="flex items-center text-black hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <div class="flex flex-col sm:flex-row w-full justify-between">
            <p class="text-2xl sm:text-3xl font-bold">Schedule Management</p>
        </div>
        
    <!-- Horizontal tabs to table -->
        <div id="caseBtn" class="space-y-4" dir="ltr" data-orientation="horizontal">
            <div role="tablist" aria-orientation="horizontal" class="inline-flex h-10 items-center justify-center rounded-md p-1 bg-gray-200" style="outline:none;">
                <button onclick="showCases();" type="button" role="tab" class="justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-inherit transition-all flex items-center gap-2 bg-white cursor-pointer hover:bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    Cases
                </button>

                <button id="appBtn" onclick="showAppointments();" type="button" role="tab" class="justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-inherit transition-all flex items-center gap-2 hover:bg-white cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                    </svg>
                    Appointments
                </button>
            </div>
    <!-- Cases Table -->
            <div id="cases" class="mt-2 ring-offset-inherit focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 space-y-4">
                <div class="rounded-lg border shadow-sm">
                    <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-2 sm:space-y-0 pb-2">
                        <div>
                            <h3 class="text-xl sm:text-2xl font-semibold leading-none tracking-tight">Cases</h3>
                            <p class="text-sm">Manage all legal cases in the system</p>
                        </div>
                        <div class="flex items-center space-x-2">

                            <button  onclick="openCaseModal()"class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm ring-offset-inherit transition-colors rounded-md px-3 ml-2 bg-black text-white h-10 font-semibold cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Case
                            </button>

                            <div id="addCaseModal" class="fixed inset-0 hidden z-50 backdrop-blur-sm">
                                <div class="absolute inset-0 bg-black opacity-10"></div>
                                <div class="relative flex items-center justify-center min-h-screen">
                                    <div class="bg-white rounded-lg p-8 max-w-md w-full m-4 relative z-50">
                                        <div class="flex justify-between items-center mb-6">
                                            <h2 class="text-2xl font-bold">Add New Case</h2>
                                            <button onclick="closeCaseModal()" class="text-gray-500 hover:text-gray-700 cursor-pointer">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <form id="addCaseForm" action="../includes/adminAddCase.php" method="post" class="grid gap-4 py-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <label for="caseNo" class="block text-gray-700 mb-2">Case Number</label>
                                                    <input type="text" name="caseNo" class="w-full border rounded-md p-2" placeholder="e.g, 2023-0157" required autocomplete="off"/>
                                                </div>
                                                <div class="space-y-2">
                                                    <label for="client" class="block text-gray-700 mb-2">Client</label>
                                                    <input type="text" name="client" class="w-full border rounded-md p-2" placeholder="Client Name" required autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <label for="lawyer" class="block text-gray-700 mb-2">Lawyer</label>
                                                    <select name="lawyer" id="" class="w-full border rounded-md p-2" required>
                                                        <option value="">Select Lawyer</option>
                                                        <?php while($lawyerRow = $lawyerResult->fetch_assoc()): ?>
                                                            <option value = "<?php echo htmlspecialchars($lawyerRow['id']); ?>"><?php echo htmlspecialchars($lawyerRow['name']); ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="space-y-2">
                                                    <label for="caseType" class="block text-gray-700 mb-2">Case Type</label>
                                                    <select name="caseType" class="w-full border rounded-md p-2" required>
                                                        <option value="">Select case type</option>
                                                        <option value="corporate litigation">Corporate Litigation</option>
                                                        <option value="family law">Family Law</option>
                                                        <option value="criminal defense">Criminal Defense</option>
                                                        <option value="estate planning">Estate Planning</option>
                                                        <option value="real estate">Real Estate</option>
                                                        <option value="IP law">IP Law</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <label for = "filingDate" class="block text-gray-700 mb-2">Filing Date</label>
                                                    <input name="filingDate" type="date" class="w-full border rounded-md p-2" required>
                                                </div>
                                                <div class="space-y-2">
                                                    <label for = "nextHearing" class="block text-gray-700 mb-2">Next Hearing</label>
                                                    <input name="nextHearing" type="date" class="w-full border rounded-md p-2" required>
                                                </div>
                                            </div>

                                            <div class="mt-4 flex justify-end space-x-3">
                                                <button type="submit" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">Add Case</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="p-4 sm:p-6 pt-0">
                        <div class="relative w-full overflow-x-auto">
                            <table class="w-full caption-bottom text-sm min-w-[600px]">
                                <thead class="border-b">
                                    <tr class="border-b transition-colors">
                                        <th class="h-12 px-4 text-left align-middle font-medium">Case ID</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium">Lawyer</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium">Type</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium">Hearing Date</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium">Status</th>
                                    </tr>
                                </thead>

                                <tbody class="border-0">
                                    <?php while($caseRow = $caseResult->fetch_assoc()): ?>
                                        <tr class="border-b transition-colors">
                                            <td class="p-4 align-middle font-medium"><?php echo htmlspecialchars($caseRow['id']); ?></td>
                                            <td class="p-4 align-middle font-medium"><?php echo htmlspecialchars($caseRow['lawyer_name']); ?></td>
                                            <td class="p-4 align-middle font-medium"><?php echo htmlspecialchars($caseRow['type']); ?></td>
                                            <td class="p-4 align-middle font-medium"><?php echo htmlspecialchars($caseRow['next_hearing']); ?></td>
                                            <td class="p-4 align-middle font-medium">
                                                <?php if($caseRow['status'] === 'open'): ?>
                                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 border-transparent text-white bg-green-500 hover:bg-green-600">Open</div>
                                                <?php else: ?>
                                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 border-transparent text-white bg-red-500 hover:bg-red-600">Closed</div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Appointments Table -->

            <div id="appointments" class="mt-2 ring-offset-inherit border rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 space-y-4 hidden">
                <div class="rounded-lg border-bg shadow-sm">
                    <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-2 sm:space-y-0 pb-2">
                        <div>
                            <h3 class="text-xl sm:text-2xl font-semibold leading-none tracking-tight">Appointments</h3>
                            <p class="text-sm">View and manage all scheduled appointments</p>
                        </div>
                        <button onclick="openAppointmentModal()" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm ring-offset-inherit transition-colors rounded-md px-3 ml-2 bg-black text-white h-10 font-semibold cursor-pointer">                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Add Appointment
                        </button>
                    </div>

                    <div id="appointmentModal" class="fixed inset-0 hidden z-50 backdrop-blur-sm">
                        <div class="absolute inset-0 bg-black opacity-10"></div>
                        <div class="relative flex items-center justify-center min-h-screen">
                            <div class="bg-white rounded-lg p-8 max-w-md w-full m-4 relative z-50">
                                <div class="flex justify-between items-center mb-6">
                                    <h2 class="text-2xl font-bold">Add New Appointment</h2>
                                    <button onclick="closeAppointmentModal()" class="text-gray-500 hover:text-gray-700 cursor-pointer">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <form id="addAppointmentForm" action="../includes/adminAddAppointment.php" method="post" class="grid gap-4 py-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label for="client" class="block text-gray-700 mb-2">Client</label>
                                            <select name="client" class="w-full border rounded-md p-2" required>
                                                <option value="">Select the Client</option>
                                                <?php while($clientRow = $clientResult->fetch_assoc()): ?>
                                                    <option value = "<?php echo htmlspecialchars($clientRow['id']); ?>"> <?php echo htmlspecialchars($clientRow['name']); ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="space-y-2">
                                            <label for="client" class="block text-gray-700 mb-2">Lawyer</label>
                                            <select name="lawyer" class="w-full border rounded-md p-2" required>
                                                <?php 
                                                    $lawStmt = $conn->prepare("SELECT * FROM users WHERE ROLE = 'lawyer'");
                                                    $lawStmt->execute();
                                                    $lawResult = $lawStmt->get_result();
                                                ?>
                                                <option value="">Select the Lawyer</option>
                                                <?php while($lawRow = $lawResult->fetch_assoc()): ?>
                                                    <option value="<?php echo htmlspecialchars($lawRow['id']); ?>"><?php echo htmlspecialchars($lawRow['name']); ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label for="appDate" class="block text-gray-700 mb-2">Appointment Date</label>
                                            <input type="date" name="appDate" class="w-full border rounded-md p-2" placeholder="Enter appointment date" required>
                                        </div>
                                        <div class="space-y-2">
                                            <label for="startTime" class="block text-gray-700 mb-2">Start Time</label>
                                            <input type="time" name="startTime" class="w-full border rounded-md p-2" placeholder="Enter start time" required>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label for="endTime" class="block text-gray-700 mb-2">End Time</label>
                                            <input type="time" name="endTime" class="w-full border rounded-md p-2" placeholder="Enter end time" required>
                                        </div>
                                        <div class="space-y-2">
                                            <label for="details" class="block text-gray-700 mb-2">Details</label>
                                            <textarea name="details" class="w-full border rounded-md p-2" placeholder="Details here(optional)"></textarea>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1">
                                        <div class="mt-4 flex justify-end space-x-3">
                                            <button type="submit" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">Add Appointment</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 pt-0">
                        <div class="relative w-full overflow-auto">
                            <table class="w-full caption-bottom text-sm">
                                <thead class="border-b">
                                    <tr class="border-b transition-colors">
                                        <th class="h-12 px-4 text-left align-middle font-medium">Appointment ID</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium">Client</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium">Lawyer</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium">Date</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium">Time</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    <?php while($appRow = $appResult->fetch_assoc()): ?>
                                        <tr class="border-b transition-colors">
                                            <td class="p-4 align-middle font-medium"><?php echo htmlspecialchars($appRow['id']); ?></td>
                                            <td class="p-4 align-middle font-medium"><?php echo htmlspecialchars($appRow['client_name']); ?></td>
                                            <td class="p-4 align-middle font-medium"><?php echo htmlspecialchars($appRow['lawyer_name']); ?></td>
                                            <td class="p-4 align-middle font-medium"><?php echo htmlspecialchars($appRow['date']); ?></td>
                                            <td class="p-4 align-middle font-medium"><?php echo htmlspecialchars($appRow['start_time']); ?> </td>
                                            <td class="p-4 align-middle font-medium">
                                                <?php if($appRow['status'] === 'confirmed'): ?>
                                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 border-transparent text-white bg-green-500 hover:bg-green-600">Confirmed</div>
                                                <?php elseif($appRow['status'] === 'pending'): ?>
                                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 border-transparent text-white bg-orange-500 hover:bg-orange-600">Pending</div>
                                                <?php else: ?>
                                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 border-transparent text-white bg-red-500 hover:bg-red-600">Cancelled</div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="../js/scheduleScript.js"></script>
</html>