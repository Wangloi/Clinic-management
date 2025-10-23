<?php include 'connection.php'; ?>
<?php include 'logging.php'; ?>
<?php
include 'security.php';

// Start secure session
start_secure_session();

// Set security headers
set_security_headers();

// Check if superadmin is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['is_superadmin']) {
    header("Location: login.php");
    exit();
}

$superadmin_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Get superadmin details
$stmt = $pdo->prepare("SELECT head_id, head_Fname, head_Lname, head_email FROM Head_Staff WHERE head_id = :superadmin_id");
$stmt->bindParam(':superadmin_id', $superadmin_id, PDO::PARAM_INT);
$stmt->execute();
$superadmin = $stmt->fetch(PDO::FETCH_ASSOC);

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$rowsPerPage = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
$offset = ($page - 1) * $rowsPerPage;

// Get total count of logs
$totalLogsCount = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM System_Logs");
    $totalLogsCount = $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Error getting total logs count: " . $e->getMessage());
}

// Calculate pagination info
$totalPages = ceil($totalLogsCount / $rowsPerPage);
$startRecord = $totalLogsCount > 0 ? $offset + 1 : 0;
$endRecord = min($offset + $rowsPerPage, $totalLogsCount);

// Get system logs for current page
function getSystemLogsWithPagination($limit, $offset) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM System_Logs
            ORDER BY timestamp DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error retrieving paginated system logs: " . $e->getMessage());
        return [];
    }
}

$systemLogs = getSystemLogsWithPagination($rowsPerPage, $offset);

// Get log statistics
$totalLogs = 0;
$todayLogs = 0;
$loginLogs = 0;
$adminActions = 0;

try {
    // Total logs count
    $stmt = $pdo->query("SELECT COUNT(*) FROM System_Logs");
    $totalLogs = $stmt->fetchColumn();
    
    // Today's logs count
    $stmt = $pdo->query("SELECT COUNT(*) FROM System_Logs WHERE DATE(timestamp) = CURDATE()");
    $todayLogs = $stmt->fetchColumn();
    
    // Login logs count
    $stmt = $pdo->query("SELECT COUNT(*) FROM System_Logs WHERE action = 'login'");
    $loginLogs = $stmt->fetchColumn();
    
    // Admin actions count (excluding login)
    $stmt = $pdo->query("SELECT COUNT(*) FROM System_Logs WHERE action != 'login'");
    $adminActions = $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Error getting log statistics: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Superadmin Dashboard | Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="../css/admin.css">
        <link rel="stylesheet" href="../css/standard-table.css">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <body>
        <div class="context flex flex-col lg:flex-row h-screen">
             <div class="sidebar hidden lg:flex lg:w-[300px] h-screen bg-white flex-col items-center fixed lg:relative">
                <div class="Name">
                    <div class="flex items-center gap-2">
                        <img src="../images/clinic.png" alt="Clinic Logo" class="h-auto"  style="width: auto; max-width: 50px">
                        <span class="text-lg md:text-xl font-semibold text-gray-800">SRCB Clinic</span>
                    </div>
                </div>

                <div class="nav">
                    <div class="text-center pt-[45px] w-full px-4"></div>
                    <div class="flex flex-col space-y-2 ">
                        <!-- Dashboard -->
                        <a href="superadmin-dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                            <img src="../images/dashboard.png" alt="Dashboard icon" class="w-5 h-5">
                            <span class="font-medium">Dashboard</span>
                        </a>

                        <!-- Account Management -->
                        <a href="superadmin-account.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-green-50 hover:text-green-600 transition-all duration-200">
                            <img src="../images/students.png" alt="Accounts icon" class="w-5 h-5">
                            <span class="font-medium">Account Management</span>
                        </a>
                        </div>
                    </div>

                    <!-- Profile Section -->
                    <div class="profile-section mt-8 pt-4 border-t border-gray-200 w-full px-4">
                        <div class="flex items-center space-x-3 mb-2 cursor-pointer" onclick="openViewProfileModal()">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                <p class="text-xs text-gray-500">Superadmin</p>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-1">
                            <button onclick="openViewProfileModal()" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-50 text-sm text-gray-700 transition-colors w-full text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Profile</span>
                            </button>
                            <a href="#" onclick="confirmLogout(event)" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-red-50 text-sm text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <button class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-md shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <div class="main-context flex-1 h-screen overflow-auto">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 pb-2 pt-[38px] pl-[60px]">System Logs</h2>
                
                <!-- Log Statistics Cards -->
                <div class="flex gap-4 mb-6 pl-[60px]">
                    <div class="bg-blue-50 p-4 rounded-lg text-center min-w-[120px]">
                        <div class="text-2xl font-bold text-blue-600"><?php echo $totalLogs; ?></div>
                        <div class="text-sm text-blue-800">Total Logs</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center min-w-[120px]">
                        <div class="text-2xl font-bold text-green-600"><?php echo $todayLogs; ?></div>
                        <div class="text-sm text-green-800">Today's Logs</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg text-center min-w-[120px]">
                        <div class="text-2xl font-bold text-purple-600"><?php echo $loginLogs; ?></div>
                        <div class="text-sm text-purple-800">Login Events</div>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg text-center min-w-[120px]">
                        <div class="text-2xl font-bold text-orange-600"><?php echo $adminActions; ?></div>
                        <div class="text-sm text-orange-800">Admin Actions</div>
                    </div>
                </div>

                <div class="content">
                <div class="standard-table-container">
                    <h3 class="standard-table-title">Recent System Activity</h3>
                    <div class="standard-table-scroll">
                        <?php if (!empty($systemLogs)): ?>
                            <table class="standard-table">
                                <!-- Table Head -->
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>User Type</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>

                                <!-- Table Body -->
                                <tbody>
                                    <?php foreach ($systemLogs as $log): ?>
                                    <tr>
                                        <!-- Timestamp -->
                                        <td>
                                            <div class="table-date">
                                                <div class="table-date-dot"></div>
                                                <?php echo date('M j, Y H:i:s', strtotime($log['timestamp'])); ?>
                                            </div>
                                        </td>

                                        <!-- User Type -->
                                        <td>
                                            <span class="table-badge <?php echo $log['user_type'] === 'Head_Staff' ? 'badge-red' : 'badge-blue'; ?>">
                                                <?php echo $log['user_type'] === 'Head_Staff' ? 'Superadmin' : 'Admin'; ?>
                                            </span>
                                        </td>

                                        <!-- Action -->
                                        <td>
                                            <span class="table-badge <?php 
                                                echo match($log['action']) {
                                                    'login' => 'badge-green',
                                                    'add' => 'badge-blue',
                                                    'edit' => 'badge-yellow',
                                                    'delete' => 'badge-red',
                                                    default => 'badge-gray'
                                                };
                                            ?>">
                                                <?php echo ucfirst(htmlspecialchars($log['action'])); ?>
                                            </span>
                                        </td>

                                        <!-- Details -->
                                        <td>
                                            <div class="table-details">
                                                <?php echo htmlspecialchars($log['details']); ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="table-empty-state">
                                <div class="table-empty-icon">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="table-empty-title">No system logs found</p>
                                <p class="table-empty-subtitle">System activity will appear here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="table-pagination">
                        <!-- Left side - Showing info -->
                        <div class="pagination-info">
                            Showing <?php echo $startRecord; ?> of <?php echo $totalLogsCount; ?> logs (Page <?php echo $page; ?> of <?php echo max(1, $totalPages); ?>)
                        </div>
                        
                        <!-- Center - Page navigation -->
                        <div class="pagination-nav">
                            <!-- First page -->
                            <a href="?page=1&rows=<?php echo $rowsPerPage; ?>" 
                               class="pagination-link <?php echo $page == 1 ? 'disabled' : ''; ?>">
                                «
                            </a>
                            
                            <!-- Previous page -->
                            <a href="?page=<?php echo max(1, $page - 1); ?>&rows=<?php echo $rowsPerPage; ?>" 
                               class="pagination-link <?php echo $page == 1 ? 'disabled' : ''; ?>">
                                ‹
                            </a>
                            
                            <!-- Current page -->
                            <span class="pagination-current">
                                <?php echo $page; ?>
                            </span>
                            
                            <!-- Next page -->
                            <a href="?page=<?php echo min($totalPages, $page + 1); ?>&rows=<?php echo $rowsPerPage; ?>" 
                               class="pagination-link <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                ›
                            </a>
                            
                            <!-- Last page -->
                            <a href="?page=<?php echo $totalPages; ?>&rows=<?php echo $rowsPerPage; ?>" 
                               class="pagination-link <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                »
                            </a>
                        </div>
                        
                        <!-- Right side - Rows per page -->
                        <div>
                            <select onchange="window.location.href='?page=1&rows=' + this.value" 
                                    class="pagination-select">
                                <option value="10" <?php echo $rowsPerPage == 10 ? 'selected' : ''; ?>>10 rows</option>
                                <option value="25" <?php echo $rowsPerPage == 25 ? 'selected' : ''; ?>>25 rows</option>
                                <option value="50" <?php echo $rowsPerPage == 50 ? 'selected' : ''; ?>>50 rows</option>
                                <option value="100" <?php echo $rowsPerPage == 100 ? 'selected' : ''; ?>>100 rows</option>
                            </select>
                        </div>
                    </div>
                </div>
                </div>
            </div>

        </div>

    <!-- Pass PHP data to JavaScript - Updated -->
    <script>
        <?php
        // Check if login success flag is set
        if (isset($_SESSION['login_success']) && $_SESSION['login_success']) {
            echo "window.showAdminLoginAlert = true;";
            echo "window.adminData = {";
            echo "fullName: " . json_encode($_SESSION['full_name']) . "";
            echo "};";
            
            // Clear the session variables after setting flag
            unset($_SESSION['login_success']);
            unset($_SESSION['login_message']);
        }
        ?>
    </script>

    <script src="../js/admin-login-success.js"></script>
    <script src="../mobile-nav.js"></script>
    <script src="../admin-dashboard.js"></script>
    <script src="../logout.js"></script>

    <?php include 'admin-profile-modals.php'; ?>
    </body>
</html>
