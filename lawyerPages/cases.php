<?php
session_start();
if (!isset($_SESSION["user_name"])) {
    header("Location: ../logins/login.php");
    exit();
}

// Debug session data
if (!isset($_SESSION['user_id'])) {
    // If user_id is not set, use a fallback method or show an error
    $_SESSION['user_id'] = $_SESSION['id'] ?? null; // Try to use 'id' if it exists
    
    if (!$_SESSION['user_id']) {
        echo "Error: User ID not found in session.";
        // You might want to redirect to login or handle this error
    }
}

require_once("../logins/dbcon.php");
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cases</title>
    <link rel = "stylesheet" href = "../css/output.css">
</head>
<body class="flex justify-center min-h-screen bg-white p-4 md:p-10">
    <div class="flex flex-col mx-auto gap-5 w-full max-w-7xl">

        <div class="self-start mb-2">
            <a href="../lawyerPages/lawyerHome.php" class="flex items-center text-black hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- After the header section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <h1 class="text-2xl sm:text-3xl text-black font-bold">Case Management</h1>
            <button onclick="openModal()" class="bg-black rounded-md text-white px-4 py-2 cursor-pointer">+ Add New Case</button>
        </div>
        
        <!-- Modal -->
        <div id="newCaseModal" class="fixed inset-0 hidden z-50 backdrop-blur-sm">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="relative flex items-center justify-center min-h-screen">
                <div class="bg-white rounded-lg p-8 max-w-md w-full m-4 relative z-50">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold ">Add New Case</h2>
                        <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
        
                    <form id="addCaseForm" action="../includes/addCase.php" method="POST" class="grid gap-4 py-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-gray-700 mb-2">Case Number</label>
                                <input name="case_number" type="text" class="w-full border rounded-md p-2" placeholder="e.g, 2023-0157" autocomplete = "off" required>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-gray-700 mb-2">Client</label>
                                <input name="client_name" type="text" class="w-full border rounded-md p-2" placeholder="Client name" autocomplete = "off" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div class="space-y-2">
                                    <label class="block text-gray-700 mb-2">Type</label>
                                    <select name="type" class="w-full border rounded-md p-2">
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
                            <div class="space-y-2">
                                <label class="block text-gray-700 mb-2">Filing Date</label>
                                <input name="filing_date" type="date" class="w-full border rounded-md p-2" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-gray-700 mb-2">Next Hearing</label>
                                <input name="next_hearing" type="date" class="w-full border rounded-md p-2" required>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">Add Case</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
         <div class="relative w-full">
            <input id="caseSearch" type="search" placeholder="Search by client name" class="w-full border border-gray-300 p-3 pl-10 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent"/>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
         </div>

         <div class="flex space-x-4 bg-gray-100 w-full sm:w-fit p-1 rounded-md overflow-x-auto">
            <button onclick="showOpenCases()" id="openCasesBtn" class="px-4 py-2 bg-white rounded-md font-medium text-black whitespace-nowrap cursor-pointer">Open Cases</button>
            <button onclick="showClosedCases()" id="closedCasesBtn" class="px-4 py-2 bg-gray-200 rounded-md font-medium text-gray-600 whitespace-nowrap cursor-pointer">Closed Cases</button>
         </div>

         <!-- Cases Tables -->
         <div class="overflow-x-auto">
            <!-- Open Cases Table -->
            <?php 
            $lawyer_id = $_SESSION['user_id'];
            $sql = "SELECT * FROM cases
                    WHERE lawyer_id = ? AND status = 'open'
                    ORDER BY filing_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $lawyer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <div id="openCasesTable" class="hidden rounded-lg overflow-hidden border border-gray-200">
              <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="p-2 md:p-4 text-gray-500 font-medium">Case Number</th>
                        <th class="p-2 md:p-4 text-gray-500 font-medium">Client</th>
                        <th class="hidden sm:table-cell p-2 md:p-4 text-gray-500 font-medium">Type</th>
                        <th class="hidden md:table-cell p-2 md:p-4 text-gray-500 font-medium">Filing Date</th>
                        <th class="hidden md:table-cell p-2 md:p-4 text-gray-500 font-medium">Next Hearing</th>
                        <th class="p-2 md:p-4 text-gray-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="p-2 md:p-4"><?php echo htmlspecialchars($row['case_number']); ?></td>
                                <td class="p-2 md:p-4"><?php echo htmlspecialchars($row['client_name']); ?></td>
                                <td class="hidden sm:table-cell p-2 md:p-4"><?php echo htmlspecialchars($row['type']); ?></td>
                                <td class="hidden md:table-cell p-2 md:p-4"><?php echo htmlspecialchars($row['filing_date']); ?></td>
                                <td class="hidden md:table-cell p-2 md:p-4"><?php echo htmlspecialchars($row['next_hearing']); ?></td>
                                <td class="p-2 md:p-4">
                                    <!-- Actions -->
                                    <div class="relative inline-block text-left">
                                        <button type="button" onclick="toggleDropdown(<?php echo $row['id']; ?>)" class="text-gray-500 hover:text-gray-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                            </svg>
                                        </button>
                                        <div id="dropdown-<?php echo $row['id']; ?>" class="hidden fixed mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black" style="z-index:9999;">
                                            <div class="py-1" role="menu" aria-orientation="vertical">
                                                <a href="../includes/closeCase.php?id=<?php echo $row['id']; ?>" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" role="menuitem" onclick="return confirm('Are you sure you want to mark this case as closed?')">Mark as Closed</a>
                                                <a href="../includes/deleteCase.php?id=<?php echo $row['id'];?>" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" role="menuitem" onclick="return confirm('Are you sure you want to delete this case?')">Delete Case</a>
                                                <a href="#" onclick="showCaseDetails(<?php echo $row['id']; ?>)" class="block px-4 py-2 text-sm text-black hover:bg-gray-100 md:hidden" role="menuitem">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">No open cases found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
              </table>
            </div>

            <!-- Closed Cases Table -->
            <?php 
            $sql = "SELECT * FROM cases
                    WHERE lawyer_id = ? AND status = 'closed'
                    ORDER BY filing_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $lawyer_id);
            $stmt->execute();
            $result = $stmt->get_result(); 
            ?>

            <div id="closedCasesTable" class="rounded-lg overflow-hidden border border-gray-200">
              <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="p-2 md:p-4 text-gray-500 font-medium">Case Number</th>
                        <th class="p-2 md:p-4 text-gray-500 font-medium">Client</th>
                        <th class="hidden sm:table-cell p-2 md:p-4 text-gray-500 font-medium">Type</th>
                        <th class="hidden md:table-cell p-2 md:p-4 text-gray-500 font-medium">Filing Date</th>
                        <th class="p-2 md:p-4 text-gray-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                     <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="p-2 md:p-4"><?php echo htmlspecialchars($row['case_number']); ?></td>
                                <td class="p-2 md:p-4"><?php echo htmlspecialchars($row['client_name']); ?></td>
                                <td class="hidden sm:table-cell p-2 md:p-4"><?php echo htmlspecialchars($row['type']); ?></td>
                                <td class="hidden md:table-cell p-2 md:p-4"><?php echo htmlspecialchars($row['filing_date']); ?></td>
                                <td class="p-2 md:p-4">
                                    <!-- Actions -->
                                    <div = "relative inline-block text-left">
                                        <button type = "button" onclick = "toggleDropdown(<?php echo $row['id']; ?>)" class = "text-gray-500 hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                        </svg>
                                        </button>
                                        <div id = "dropdown-<?php echo $row['id']; ?>" class = "hidden fixed mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black" style = "z-index:9999;">
                                            <div class = "py-1" role = "menu" aria-orientation = "vertical">
                                                <a href="../includes/reopenCase.php?id=<?php echo $row['id']; ?>" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" role="menuitem" onclick="return confirm('Are you sure you want to reopen this case?')">Reopen Case</a>
                                                <a href="../includes/deleteCase.php?id=<?php echo $row['id'];?>" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" role="menuitem" onclick="return confirm('Are you sure you want to delete this case?')">Delete Case</a>
                                                <a href="#" onclick="showCaseDetails(<?php echo $row['id']; ?>)" class="block px-4 py-2 text-sm text-black hover:bg-gray-100 md:hidden" role="menuitem">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                     <?php else:?>
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">No closed cases</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
              </table>
            </div>
         </div>
    </div>
    <script src="../js/caseScript.js"></script>
</body>
</html>