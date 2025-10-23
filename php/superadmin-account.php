<?php include 'user-role.php'; ?>
<?php include 'verifyer.php'; ?>
<?php include 'accounts_data.php'; ?>

<?php
// Fetch accounts data
$data = getAccountsData($_GET);
extract($data);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin| Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="../css/superadmin.css">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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
                        <a href="superadmin-dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                            <img src="../images/dashboard.png" alt="Dashboard icon" class="w-5 h-5">
                            <span class="font-medium">Dashboard</span>
                        </a>

                        <a href="superadmin-account.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-pink-50 hover:text-pink-600 transition-all duration-200">
                            <img src="../images/reports.png" alt="Reports icon" class="w-5 h-5">
                            <span class="font-medium">Account Management</span>
                        </a>
                        </div>
                    </div>
                    <!-- Profile Section -->
                    <div class="profile-section mt-8 pt-4 border-t border-gray-200 w-full px-4">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-1">
                            <button onclick="openViewProfileModal()" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-50 text-sm text-gray-700 transition-colors w-full text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Profile</span>
                            </a>
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
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 pb-2 pt-[38px] pl-[60px]">Account Management</h2>
                <div class="account-info" style="width: 1145px; min-height: 500px; background-color: #ffffff; margin: 0 auto; border-radius: 25px; padding: 20px;">
                    <div class="container mx-auto px-4 py-8">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                            <h2 class="text-xl font-bold text-gray-800">Accounts Information</h2>
                            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-center">
                                <form method="GET" class="flex flex-wrap gap-3">
                                    <input type="hidden" name="page" value="1">
                                    <input
                                        type="text"
                                        name="search"
                                        placeholder="Search accounts..."
                                        value="<?php echo htmlspecialchars($filters['search']); ?>"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-40"
                                    >
                                    <select name="sort" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                                        <option value="name-asc" <?php echo $filters['sort'] === 'name-asc' ? 'selected' : ''; ?>>Name A-Z</option>
                                        <option value="name-desc" <?php echo $filters['sort'] === 'name-desc' ? 'selected' : ''; ?>>Name Z-A</option>
                                        <option value="email-asc" <?php echo $filters['sort'] === 'email-asc' ? 'selected' : ''; ?>>Email A-Z</option>
                                        <option value="email-desc" <?php echo $filters['sort'] === 'email-desc' ? 'selected' : ''; ?>>Email Z-A</option>
                                        <option value="role-asc" <?php echo $filters['sort'] === 'role-asc' ? 'selected' : ''; ?>>Role A-Z</option>
                                        <option value="role-desc" <?php echo $filters['sort'] === 'role-desc' ? 'selected' : ''; ?>>Role Z-A</option>
                                    </select>
                                    <select name="role" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                                        <option value="">All Roles</option>
                                        <option value="Clinic Staff" <?php echo $filters['role'] === 'Clinic Staff' ? 'selected' : ''; ?>>Clinic Staff</option>
                                        <option value="Superadmin" <?php echo $filters['role'] === 'Superadmin' ? 'selected' : ''; ?>>Superadmin</option>
                                    </select>
                                    <select name="rows" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                                        <option value="5" <?php echo $rows_per_page == 5 ? 'selected' : ''; ?>>5 rows</option>
                                        <option value="10" <?php echo $rows_per_page == 10 ? 'selected' : ''; ?>>10 rows</option>
                                        <option value="20" <?php echo $rows_per_page == 20 ? 'selected' : ''; ?>>20 rows</option>
                                        <option value="50" <?php echo $rows_per_page == 50 ? 'selected' : ''; ?>>50 rows</option>
                                        <option value="100" <?php echo $rows_per_page == 100 ? 'selected' : ''; ?>>100 rows</option>
                                    </select>
                                </form>
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm" onclick="openAddAccountModal()">
                                    Add Account
                                </button>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow overflow-hidden mx-auto">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 mx-auto">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Full Name
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Role
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Last Login
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php if (!empty($accounts)): ?>
                                            <?php foreach ($accounts as $account): ?>
                                            <tr class="hover:bg-gray-50">
                                                <!-- Full Name -->
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars(getAccountFullName($account)); ?>
                                                    </div>
                                                </td>

                                                <!-- Email -->
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($account['email']); ?></div>
                                                </td>

                                                <!-- Role -->
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    <?php if ($account['role'] === 'Superadmin'): ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                            Superadmin
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            Clinic Staff
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Last Login -->
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    Never
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    No accounts found. <?php echo !empty($filters['search']) ? 'Try a different search term.' : ''; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-gray-700">
                                Showing <?php echo count($accounts); ?> of <?php echo $total_accounts; ?> accounts
                                (Page <?php echo $current_page; ?> of <?php echo max(1, $total_pages); ?>)
                            </div>

                            <div class="flex gap-1 justify-center">
                                <!-- First Page -->
                                <button onclick="goToPage(1)" <?php echo $current_page <= 1 ? 'disabled' : ''; ?>
                                    class="px-2 py-1 border rounded text-xs <?php echo $current_page <= 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>">
                                    &laquo;
                                </button>

                                <!-- Previous Page -->
                                <button onclick="goToPage(<?php echo $current_page - 1; ?>)" <?php echo $current_page <= 1 ? 'disabled' : ''; ?>
                                    class="px-2 py-1 border rounded text-xs <?php echo $current_page <= 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>">
                                    &lsaquo;
                                </button>

                                <!-- Page Numbers -->
                                <?php
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $start_page + 4);
                                $start_page = max(1, $end_page - 4);

                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                    <button onclick="goToPage(<?php echo $i; ?>)" 
                                        class="px-2 py-1 border rounded text-xs <?php echo $i == $current_page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </button>
                                <?php endfor; ?>

                                <!-- Next Page -->
                                <button onclick="goToPage(<?php echo $current_page + 1; ?>)" <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>
                                    class="px-2 py-1 border rounded text-xs <?php echo $current_page >= $total_pages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>">
                                    &rsaquo;
                                </button>

                                <!-- Last Page -->
                                <button onclick="goToPage(<?php echo $total_pages; ?>)" <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>
                                    class="px-2 py-1 border rounded text-xs <?php echo $current_page >= $total_pages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>">
                                    &raquo;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'add-account-modal.html'; ?>
        </div>

    <script>
        function goToPage(page) {
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthText = document.getElementById('strength-text');

            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            const isLongEnough = password.length >= 8;

            let strength = 0;
            let feedback = [];

            if (isLongEnough) {
                strength++;
            } else {
                feedback.push('At least 8 characters');
            }

            if (hasUpperCase) {
                strength++;
            } else {
                feedback.push('Uppercase letter');
            }

            if (hasLowerCase) {
                strength++;
            } else {
                feedback.push('Lowercase letter');
            }

            if (hasNumbers) {
                strength++;
            } else {
                feedback.push('Number');
            }

            if (hasSpecialChar) {
                strength++;
            } else {
                feedback.push('Special character');
            }

            // Update visual bars
            for (let i = 1; i <= 5; i++) {
                const bar = document.getElementById(`strength-bar-${i}`);
                if (i <= strength) {
                    if (strength >= 4) {
                        bar.className = 'h-1 w-1/5 bg-green-500 rounded';
                    } else if (strength >= 3) {
                        bar.className = 'h-1 w-1/5 bg-yellow-500 rounded';
                    } else {
                        bar.className = 'h-1 w-1/5 bg-red-500 rounded';
                    }
                } else {
                    bar.className = 'h-1 w-1/5 bg-gray-200 rounded';
                }
            }

            // Update text
            let color = 'text-red-500';
            let text = 'Weak';

            if (strength >= 4) {
                color = 'text-green-500';
                text = 'Strong';
            } else if (strength >= 3) {
                color = 'text-yellow-500';
                text = 'Medium';
            }

            strengthText.className = `text-xs ${color}`;
            strengthText.innerHTML = `${text}${feedback.length > 0 ? ' - Missing: ' + feedback.join(', ') : ''}`;
        }

        function validatePassword(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // Check if passwords match
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Passwords do not match.',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Check password requirements
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            const isLongEnough = password.length >= 8;

            if (!hasUpperCase || !hasLowerCase || !hasNumbers || !hasSpecialChar || !isLongEnough) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Show loading SweetAlert
            Swal.fire({
                title: 'Creating Account...',
                text: 'Please wait while we create the new account.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: decodeURIComponent(urlParams.get('success')),
                    confirmButtonText: 'OK'
                });
            } else if (urlParams.has('error')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: decodeURIComponent(urlParams.get('error')),
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
    <script src="../mobile-nav.js"></script>
    <script src="../modal.js"></script>
    <script src="../logout.js"></script>
    <script src="add-account-modal.js"></script>
    <?php include 'admin-profile-modals.php'; ?>
    </body>
</html>
