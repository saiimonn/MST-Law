<?php 
session_start();
if(!isset($_SESSION['user_name']) || !isset($_SESSION['user_id'])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../logins/dbcon.php");

$sql = "SELECT * FROM users
        WHERE role = 'lawyer' OR role = 'client' OR role = 'admin'";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MST LAW</title>
    <link rel="stylesheet" href="../css/output.css">
</head>
<body class="flex flex-col min-h-screen py-6 px-2 sm:px-6 md:px-16 lg:px-52" style="font-family: 'Inter', sans-serif;">
    <div class="flex flex-col justify-start w-full sm:w-3/5 md:w-2/5 lg:w-3/12">
        <a href="adminHome.php" class="flex text-gray-800 hover:text-black">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
              </svg>          
            <span class = "ml-4">Back to Dashboard</span>
        </a>
        <h2 class="text-black text-xl sm:text-2xl mt-6 font-bold">Users</h2>
    </div>
<!-- Searchbar + modals start -->
    <div class="flex flex-col space-y-4 md:flex-row md:space-y-0 md:space-x-4 mt-4">
        <div class="flex-1 relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <input type="text" class="w-full h-10 rounded-md border bg-inherit px-3 pl-10 pr-3 py-2 text-sm ring-offset-inherit" placeholder="Search by name">
        </div>
        
        <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">

            <button onclick = "openStatusModal()" type = "button" role = "combobox" class = "flex h-10 items-center justify-between rounded-md border bg-inherit px-3 py-2 text-sm ring-offset-inherit">
                <span>All Statuses</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>                  
            </button>

            <div id = "statusModal" class = "hidden absolute mt-12 mr-36 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                <div class = "py-1 text-sm text-gray-700" role = "menu" aria-orientation = "vertical">
                    <button onclick = "selectStatus('all statuses')" class = "block w-full px-4 py-2 text-left hover:bg-gray-100">All Statuses</button>
                    <button onclick = "selectStatus('active')" class = "block w-full px-4 py-2 text-left hover:bg-gray-100">Active</button>
                    <button onclick = "selectStatus('inactive')" class = "block w-full px-4 py-2 text-left hover:bg-gray-100">Inactive</button>
                </div>
            </div>

            <button onclick = "openRoleModal()" type = "button" role = "combobox" class = "flex h-10 items-center justify-between rounded-md border bg-inherit px-3 py-2 text-sm ring-offset-inherit">
                <span>All Roles</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>                  
            </button>

            <div id = "roleModal" class = "hidden absolute mt-12 ml-28 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                <div class = "py-1 text-sm text-gray-700" role = "menu" aria-orientation = "vertical">
                    <button onclick = "selectRole('All Roles')" class = "block w-full px-4 py-2 text-left hover:bg-gray-100">All Roles</button>
                    <button onclick = "selectRole('client')" class = "block w-full px-4 py-2 text-left hover:bg-gray-100">Client</button>
                    <button onclick = "selectRole('lawyer')" class = "block w-full px-4 py-2 text-left hover:bg-gray-100">Lawyer</button>
                </div>
            </div>


        </div>
    </div>
<!-- Searchbar + modals end -->

    <div class="ring-offset-inherit focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 mt-6">
        <div id="user-table-container" class="rounded-md border overflow-x-auto">
            <div class="grid grid-cols-10 gap-2 p-4 font-bold min-w-[600px]">
                <div class="col-span-3">User</div>
                <div class="col-span-2">Role</div>
                <div class="col-span-3">Email</div>
                <div class="col-span-2">Status</div>
            </div>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="grid grid-cols-10 gap-2 p-4 font-medium border-t user-row min-w-[600px]" data-role="<?php echo htmlspecialchars($row['role']); ?>" data-user-id="<?php echo $row['id']; ?>">
                    <div class="col-span-3"><?php echo htmlspecialchars($row['name']); ?></div>
                    <div class="col-span-2">
                        <select class="role-select border rounded px-2 py-1" data-user-id="<?php echo $row['id']; ?>">
                            <option value="client" <?php if($row['role'] === 'client') echo 'selected'; ?>>Client</option>
                            <option value="lawyer" <?php if($row['role'] === 'lawyer') echo 'selected'; ?>>Lawyer</option>
                            <option value="admin" <?php if($row['role'] === 'admin') echo 'selected'; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="col-span-3 overflow-hidden text-ellipsis whitespace-nowrap"><?php echo htmlspecialchars($row['email']); ?></div>
                    <div class="col-span-2">
                        <?php if($row['status'] === 'active'):?>
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 border-transparent text-white bg-green-500 hover:bg-green-600">Active</div>
                        <?php else: ?>
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 border-transparent text-white bg-red-500 hover:bg-red-600">Inactive</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
<script src="../js/userStatus.js"></script>
</html>