<?php
include 'connection.php';

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
        $user = ['fname' => '', 'mname' => '', 'lname' => '', 'email' => '', 'contact_number' => ''];
    }
} catch (PDOException $e) {
    $user = ['fname' => '', 'mname' => '', 'lname' => '', 'email' => '', 'contact_number' => ''];
}
?>

<!-- View Profile Modal -->
<div id="viewProfileModal" class="fixed inset-0 backdrop-blur hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">View Profile</h3>
            <button onclick="closeViewProfileModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-xl">
                        <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($user['fname'] . ' ' . ($user['mname'] ? $user['mname'] . ' ' : '') . $user['lname']); ?></h4>
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                        <p class="text-sm text-gray-500">Administrator</p>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user['fname']); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user['mname'] ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user['lname']); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        <?php if ($user_type === 'clinic'): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user['contact_number'] ?? 'N/A'); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button onclick="closeViewProfileModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Close
                    </button>
                    <button onclick="openProfileModal()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Edit Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div id="profileModal" class="fixed inset-0 backdrop-blur hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Edit Profile</h3>
            <button onclick="closeProfileModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <form action="update_profile.php" method="POST" class="space-y-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['fname']); ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user['mname'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['lname']); ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <?php if ($user_type === 'clinic'): ?>
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <?php endif; ?>

                <!-- Password Change Section -->
                <div class="border-t pt-4">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Change Password</h4>
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <p id="password-error" class="mt-1 text-sm text-red-600 hidden">Passwords do not match</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeProfileModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openViewProfileModal() {
        document.getElementById('viewProfileModal').classList.remove('hidden');
    }

    function closeViewProfileModal() {
        document.getElementById('viewProfileModal').classList.add('hidden');
    }

    function openProfileModal() {
        closeViewProfileModal(); // Close view modal if open
        document.getElementById('profileModal').classList.remove('hidden');
    }

    function closeProfileModal() {
        document.getElementById('profileModal').classList.add('hidden');
    }

    // Close modals when clicking outside
    document.getElementById('viewProfileModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeViewProfileModal();
        }
    });

    document.getElementById('profileModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeProfileModal();
        }
    });

    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = this.value;
        const errorElement = document.getElementById('password-error');

        if (confirmPassword && newPassword !== confirmPassword) {
            errorElement.classList.remove('hidden');
        } else {
            errorElement.classList.add('hidden');
        }
    });

    document.getElementById('new_password').addEventListener('input', function() {
        const confirmPassword = document.getElementById('confirm_password').value;
        const errorElement = document.getElementById('password-error');

        if (confirmPassword && this.value !== confirmPassword) {
            errorElement.classList.remove('hidden');
        } else {
            errorElement.classList.add('hidden');
        }
    });

    // Form validation before submit
    document.querySelector('form[action="update_profile.php"]').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const currentPassword = document.getElementById('current_password').value;

        // If any password field is filled, validate
        if (currentPassword || newPassword || confirmPassword) {
            if (!currentPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Current Password Required',
                    text: 'Please enter your current password to change it.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (!newPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'New Password Required',
                    text: 'Please enter a new password.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'New password and confirmation do not match.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (newPassword.length < 8) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Too Weak',
                    text: 'New password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Check password complexity
            const hasUpperCase = /[A-Z]/.test(newPassword);
            const hasLowerCase = /[a-z]/.test(newPassword);
            const hasNumbers = /\d/.test(newPassword);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(newPassword);

            if (!hasUpperCase || !hasLowerCase || !hasNumbers || !hasSpecialChar) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Too Weak',
                    text: 'New password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                    confirmButtonText: 'OK'
                });
                return;
            }
        }
    });

    // Check for success message
    <?php if (isset($_GET['profile_updated'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Profile Updated!',
            text: 'Your profile has been updated successfully.',
            confirmButtonText: 'OK'
        });
    <?php elseif (isset($_GET['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Update Failed!',
            text: '<?php echo htmlspecialchars($_GET['error']); ?>',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
</script>
