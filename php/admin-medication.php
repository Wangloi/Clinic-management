<?php include 'user-role.php'; ?>
<?php include 'verifyer.php'; ?>
<?php include 'connection.php'; ?>
<?php include 'admin-user-data.php'; ?>
<?php
include 'medicine_data.php';
$search = $_GET['search'] ?? '';
$medicines = getAllMedicines($search);
$total_medicines = count($medicines);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin| Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="../css/admin.css">
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
                        <!-- Dashboard -->
                        <a href="admin-dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                            <img src="../images/dashboard.png" alt="Dashboard icon" class="w-5 h-5">
                            <span class="font-medium">Dashboard</span>
                        </a>

                        <!-- Students -->
                        <a href="admin-students.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-green-50 hover:text-green-600 transition-all duration-200">
                            <img src="../images/students.png" alt="Students icon" class="w-5 h-5">
                            <span class="font-medium">Students</span>
                        </a>

                        <!-- Clinic Visits -->
                        <a href="admin-visits.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-all duration-200">
                            <img src="../images/clinic-visit.png" alt="Clinic visits icon" class="w-5 h-5">
                            <span class="font-medium">Clinic Visits</span>
                        </a>

                        <!-- Medication -->
                        <a href="admin-medication.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-yellow-50 hover:text-yellow-600 transition-all duration-200">
                            <img src="../images/medication.png" alt="Medication icon" class="w-5 h-5">
                            <span class="font-medium">Medication</span>
                        </a>

                        <!-- Appointments -->
                        <a href="admin-appointment.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-red-50 hover:text-red-600 transition-all duration-200">
                            <img src="../images/appointments.png" alt="Appointments icon" class="w-5 h-5">
                            <span class="font-medium">Appointments</span>
                        </a>

                        <!-- Reports -->
                        <a href="admin-reports.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-200">
                            <img src="../images/reports.png" alt="Reports icon" class="w-5 h-5">
                            <span class="font-medium">Reports</span>
                        </a>
                        <!-- Help Center -->
                         <a href="admin-help.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-pink-50 hover:text-pink-600 transition-all duration-200">
                            <img src="../images/reports.png" alt="Reports icon" class="w-5 h-5">
                            <span class="font-medium">Help Center</span>
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
                                <p class="text-xs text-gray-500">Administrator</p>
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
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 pb-2 pt-[38px] pl-[60px]">Medication</h2>
                <div class="Recent">
                    <div class="student-info" style="width: 1145px; min-height: 500px; background-color: #ffffff; margin: 0 auto; border-radius: 25px; padding: 20px;">
                        <div class="container mx-auto px-4 py-8">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                                <h2 class="text-xl font-bold text-gray-800">Medication Inventory</h2>
                                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                                    <form method="GET" class="flex gap-3">
                                        <input
                                            type="text"
                                            name="search"
                                            placeholder="Search medicines..."
                                            value="<?php echo htmlspecialchars($search); ?>"
                                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-40"
                                        >
                                    </form>
                                    <button id="add-medicine-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        Add Medicine
                                    </button>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg shadow overflow-hidden mx-auto">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 mx-auto">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Medicine Name
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Description
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Quantity
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Unit
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Expiration Date
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Batch No
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <?php if (!empty($medicines)): ?>
                                                <?php foreach ($medicines as $medicine): ?>
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($medicine['medicine_name']); ?></td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($medicine['description']); ?></td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($medicine['quantity']); ?></td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($medicine['unit']); ?></td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($medicine['expiration_date']); ?></td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($medicine['batch_no'] ?? ''); ?></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <div class="flex justify-center space-x-2">
                                                        <button class="text-blue-600 hover:text-blue-900" title="Edit" onclick='openEditModal(<?php echo htmlspecialchars(json_encode($medicine), ENT_QUOTES); ?>)'>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                        </button>
                                                        <button class="text-red-600 hover:text-red-900" title="Delete" onclick='deleteMedicine(<?php echo json_encode($medicine['medicine_id']); ?>, <?php echo json_encode($medicine['medicine_name']); ?>)'>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <button class="text-green-600 hover:text-green-900" title="View Dispensing History" onclick='viewMedicineHistory(<?php echo json_encode($medicine['medicine_id']); ?>, <?php echo json_encode($medicine['medicine_name']); ?>)'>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="px-4 py-3 text-center text-sm text-gray-500">No medicines found. Add some using the button above.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div class="text-sm text-gray-700">
                                    Showing <?php echo $total_medicines; ?> of <?php echo $total_medicines; ?> medicines
                                </div>

                                <div class="flex gap-1 justify-center">
                                    <!-- Pagination buttons can be added here if needed -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
        </div>

    <!-- Add Medicine Modal -->
    <div id="addMedicineModal" class="fixed inset-0 flex items-center justify-center z-[9999] hidden backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto relative z-[10000] pointer-events-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">Add New Medicine</h3>
                <span class="close absolute top-4 right-4 text-gray-400 hover:text-gray-600 cursor-pointer text-2xl" onclick="closeModal('addMedicineModal')">&times;</span>
            </div>

            <div class="px-6 py-4">
                <form id="addMedicineForm" method="POST" action="add-medicine.php">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Medicine Name</label>
                            <input type="text" id="add-medicine-name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <input type="number" id="add-quantity" name="quantity" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="add-description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                            <select id="add-unit" name="unit" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Unit</option>
                                <option value="Tablets">Tablets</option>
                                <option value="Capsules">Capsules</option>
                                <option value="Bottles">Bottles</option>
                                <option value="Vials">Vials</option>
                                <option value="Sachets">Sachets</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Batch No</label>
                            <input type="text" id="add-batch-no" name="batch_no" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiration Date</label>
                        <input type="date" id="add-expiration-date" name="expiration_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('addMedicineModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" form="addMedicineForm" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Add Medicine
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Medicine Modal -->
    <div id="editMedicineModal" class="fixed inset-0 flex items-center justify-center z-[9999] hidden backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto relative z-[10000] pointer-events-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">Edit Medicine</h3>
                <span class="close absolute top-4 right-4 text-gray-400 hover:text-gray-600 cursor-pointer text-2xl" onclick="closeModal('editMedicineModal')">&times;</span>
            </div>

            <div class="px-6 py-4">
                <form id="editMedicineForm" method="POST" action="edit-medicine.php">
                    <input type="hidden" id="edit-medicine-id" name="id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Medicine Name</label>
                            <input type="text" id="edit-medicine-name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <input type="number" id="edit-quantity" name="quantity" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="edit-description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                            <select id="edit-unit" name="unit" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Unit</option>
                                <option value="Tablets">Tablets</option>
                                <option value="Capsules">Capsules</option>
                                <option value="Bottles">Bottles</option>
                                <option value="Vials">Vials</option>
                                <option value="Sachets">Sachets</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Batch No</label>
                            <input type="text" id="edit-batch-no" name="batch_no" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiration Date</label>
                        <input type="date" id="edit-expiration-date" name="expiration_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('editMedicineModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="submitEditMedicineForm()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Update Medicine
                </button>
            </div>
        </div>
    </div>

    <!-- Medication History Modal -->
    <div id="medicationHistoryModal" class="fixed inset-0 flex items-center justify-center z-[2000] hidden backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-6xl mx-4 max-h-[90vh] overflow-y-auto relative">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-800">Medication Dispensing History</h3>
                <span class="text-gray-400 hover:text-gray-600 cursor-pointer text-2xl" onclick="closeModal('medicationHistoryModal')">&times;</span>
            </div>

            <div class="px-6 py-4">
                <!-- Loading State -->
                <div id="historyLoading" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Loading dispensing history...</p>
                </div>

                <!-- History Content -->
                <div id="historyContent" class="hidden">
                    <!-- Medicine Info -->
                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <h4 class="text-lg font-semibold text-blue-800 mb-2">💊 <span id="medicineName">Medicine Name</span></h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">Total Dispensed:</span>
                                <span id="totalDispensed" class="font-semibold text-blue-700">0</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Total Records:</span>
                                <span id="totalRecords" class="font-semibold text-blue-700">0</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Last Dispensed:</span>
                                <span id="lastDispensed" class="font-semibold text-blue-700">Never</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dispensing List -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h5 class="text-lg font-medium text-gray-900">Dispensing Records</h5>
                        </div>
                        <div id="dispensingList" class="divide-y divide-gray-200">
                            <!-- Dispensing records will be populated here -->
                        </div>
                    </div>

                    <!-- No History State -->
                    <div id="noHistory" class="text-center py-8 hidden">
                        <div class="text-6xl mb-4">📋</div>
                        <h5 class="text-lg font-medium text-gray-700 mb-2">No Dispensing History</h5>
                        <p class="text-gray-600">This medication has not been dispensed yet.</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button type="button" onclick="closeModal('medicationHistoryModal')" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script src="../modal.js"></script>
    <script src="../admin-medication.js"></script>
    <script src="../mobile-nav.js"></script>
    <script src="../logout.js"></script>
    
    <script>
    // Close all modals to prevent layering issues
    function closeAllModals() {
        const modals = [
            'addMedicineModal',
            'editMedicineModal', 
            'medicationHistoryModal'
        ];
        
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
        });
        
        // Remove any backdrop blur from body
        document.body.style.overflow = 'auto';
    }

    // Enhanced modal close function
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    // Add keyboard event listener for Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAllModals();
        }
    });

    // Add click outside modal to close
    document.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('[id$="Modal"]');
        modals.forEach(modal => {
            if (!modal.classList.contains('hidden') && event.target === modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
    });
    
    // Function to view medication dispensing history
    async function viewMedicineHistory(medicineId, medicineName) {
        try {
            console.log('Opening history for:', medicineName, 'ID:', medicineId);
            
            // Close any other open modals first
            closeAllModals();
            
            // Get modal elements
            const modal = document.getElementById('medicationHistoryModal');
            const loading = document.getElementById('historyLoading');
            const content = document.getElementById('historyContent');
            const dispensingList = document.getElementById('dispensingList');
            
            if (!modal || !loading || !content || !dispensingList) {
                throw new Error('Modal elements not found');
            }
            
            // CLEAR PREVIOUS DATA FIRST
            document.getElementById('medicineName').textContent = 'Loading...';
            document.getElementById('totalDispensed').textContent = '0';
            document.getElementById('totalRecords').textContent = '0';
            document.getElementById('lastDispensed').textContent = 'Never';
            dispensingList.innerHTML = ''; // Clear list
            
            // Show modal with loading state
            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            content.classList.add('hidden');
            document.body.style.overflow = 'hidden';
            
            // Set medicine name after clearing
            document.getElementById('medicineName').textContent = medicineName;
            
            console.log('Fetching data for medicine_id:', medicineId);
            
            // Use simple working API
            const response = await fetch(`simple-working-api.php?medicine_id=${medicineId}&t=${Date.now()}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Failed to load history');
            }
            
            // Handle real dispensing data
            console.log('API Response:', data);
            
            const history = data.data || [];
            const totalDispensed = data.total_dispensed || 0;
            
            document.getElementById('totalDispensed').textContent = totalDispensed;
            document.getElementById('totalRecords').textContent = history.length;
            
            if (history.length > 0) {
                document.getElementById('lastDispensed').textContent = formatDateTime(history[0].dispensed_at);
                
                // Clear and populate list with dispensing records
                dispensingList.innerHTML = '';
                history.forEach((record, index) => {
                    const listItem = document.createElement('div');
                    listItem.className = 'px-6 py-4 hover:bg-gray-50';
                    listItem.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">${index + 1}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">
                                            ${record.quantity_dispensed || 0} ${record.unit || 'units'} dispensed
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            ${formatDate(record.dispensed_at || record.visit_date)} • ${record.student_name || 'Unknown Student'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Dispensed
                                </span>
                            </div>
                        </div>
                    `;
                    dispensingList.appendChild(listItem);
                });
                
                const noHistory = document.getElementById('noHistory');
                if (noHistory) noHistory.classList.add('hidden');
            } else {
                document.getElementById('lastDispensed').textContent = 'Never';
                const noHistory = document.getElementById('noHistory');
                if (noHistory) noHistory.classList.remove('hidden');
            }
            
            // Hide loading and show content
            loading.classList.add('hidden');
            content.classList.remove('hidden');
            
        } catch (error) {
            console.error('Error loading medication history:', error);
            alert('Failed to load medication history: ' + error.message);
            
            // Hide the modal on error and restore scrolling
            closeAllModals();
        }
    }
    
    // Helper function to format date
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }
    
    // Helper function to format date and time
    function formatDateTime(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    </script>
    
    <?php include 'admin-profile-modals.php'; ?>
    </body>
</html>
