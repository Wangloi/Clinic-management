<?php include 'connection.php'; ?>
<?php include 'security.php'; ?>
<?php include 'user-role.php'; ?>
<?php include 'verifyer.php'; ?>

<?php
// Fetch user profile data
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

try {
    if ($user_type === 'clinic') {
        $stmt = $pdo->prepare("SELECT clinic_email as email, clinic_Fname as fname, clinic_Mname as mname, clinic_Lname as lname, contact_number FROM Clinic_Staff WHERE staff_id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT head_email as email, head_Fname as fname, head_Mname as mname, head_Lname as lname FROM Head_Staff WHERE head_id = ?");
    }
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Integrated Digital Clinic Management System</title>
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="context flex flex-col lg:flex-row h-screen">
        <div class="sidebar hidden lg:flex lg:w-[300px] h-screen bg-white flex-col items-center fixed lg:relative">
            <div class="Name">
                <div class="flex items-center gap-2">
                    <img src="../images/clinic.png" alt="Clinic Logo" class="h-auto" style="width: auto; max-width: 50px">
                    <span class="text-lg md:text-xl font-semibold text-gray-800">SRCB Clinic</span>
                </div>
            </div>

            <div class="nav">
                <div class="text-center pt-[45px] w-full px-4"></div>
                <div class="flex flex-col space-y-2">
                    <?php if ($_SESSION['is_superadmin']): ?>
                        <a href="superadmin-dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                            <img src="../images/dashboard.png" alt="Dashboard icon" class="w-5 h-5">
                            <span class="font-medium">Dashboard</span>
                        </a>
                        <a href="superadmin-account.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-pink-50 hover:text-pink-600 transition-all duration-200">
                            <img src="../images/reports.png" alt="Reports icon" class="w-5 h-5">
                            <span class="font-medium">Account Management</span>
                        </a>
                    <?php else: ?>
                        <a href="admin-dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                            <img src="../images/dashboard.png" alt="Dashboard icon" class="w-5 h-5">
                            <span class="font-medium">Dashboard</span>
                        </a>
                        <a href="admin-students.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-green-50 hover:text-green-600 transition-all duration-200">
                            <img src="../images/students.png" alt="Students icon" class="w-5 h-5">
                            <span class="font-medium">Students</span>
                        </a>
                        <a href="admin-visits.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-all duration-200">
                            <img src="../images/clinic-visit.png" alt="Clinic visits icon" class="w-5 h-5">
                            <span class="font-medium">Clinic Visits</span>
                        </a>
                        <a href="admin-medication.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-yellow-50 hover:text-yellow-600 transition-all duration-200">
                            <img src="../images/medication.png" alt="Medication icon" class="w-5 h-5">
                            <span class="font-medium">Medication</span>
                        </a>
                        <a href="admin-appointment.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-red-50 hover:text-red-600 transition-all duration-200">
                            <img src="../images/appointments.png" alt="Appointments icon" class="w-5 h-5">
                            <span class="font-medium">Appointments</span>
                        </a>
                        <a href="admin-reports.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-200">
                            <img src="../images/reports.png" alt="Reports icon" class="w-5 h-5">
                            <span class="font-medium">Reports</span>
                        </a>
                        <a href="admin-help.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-pink-50 hover:text-pink-600 transition-all duration-200">
                            <img src="../images/reports.png" alt="Reports icon" class="w-5 h-5">
                            <span class="font-medium">Help Center</span>
                        </a>
                    <?php endif; ?>
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
                        <p class="text-xs text-gray-500"><?php echo $_SESSION['is_superadmin'] ? 'Superadmin' : 'Administrator'; ?></p>
                    </div>
                </div>
                <div class="flex flex-col space-y-1">
                    <a href="profile.php" class="flex items-center space-x-2 p-2 rounded-lg bg-gray-100 text-sm text-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Profile</span>
                    </a>
                    <a href="logout.php" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-red-50 text-sm text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>

        <button class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-md shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <div class="main-context flex-1 h-screen overflow-auto">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 pb-2 pt-[38px] pl-[60px]">My Profile</h2>
            <div class="profile-container" style="width: 800px; min-height: 500px; background-color: #ffffff; margin: 0 auto; border-radius: 25px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <form id="profileForm" method="POST" action="update_profile.php">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['fname']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user['mname'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['lname']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <?php if ($user_type === 'clinic'): ?>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="contact_number">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <?php endif; ?>

                    <hr class="my-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h3>
                    <p class="text-sm text-gray-600 mb-4">Leave blank if you don't want to change your password.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div></div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" oninput="checkPasswordStrength()">
                            <div id="password-strength" class="mt-2">
                                <div class="flex space-x-1 mb-1">
                                    <div id="strength-bar-1" class="h-1 w-1/5 bg-gray-200 rounded"></div>
                                    <div id="strength-bar-2" class="h-1 w-1/5 bg-gray-200 rounded"></div>
                                    <div id="strength-bar-3" class="h-1 w-1/5 bg-gray-200 rounded"></div>
                                    <div id="strength-bar-4" class="h-1 w-1/5 bg-gray-200 rounded"></div>
                                    <div id="strength-bar-5" class="h-1 w-1/5 bg-gray-200 rounded"></div>
                                </div>
                                <div id="strength-text" class="text-xs text-gray-500"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="window.history.back()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('new_password').value;
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

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const currentPassword = document.getElementById('current_password').value;

            if ((newPassword || confirmPassword) && !currentPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please enter your current password to change it.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'New passwords do not match.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (newPassword && newPassword.length < 8) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'New password must be at least 8 characters long.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Updating Profile...',
                text: 'Please wait while we update your profile.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        // Check for success/error messages
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
</body>
</html>
