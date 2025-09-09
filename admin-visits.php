<?php include 'user-role.php'; ?>
<?php include 'verifyer.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin| Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="admin.css">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link rel="stylesheet" href="modal.css">
    </head>

    <body>
        <div class="context flex flex-col lg:flex-row h-screen">
             <div class="sidebar hidden lg:flex lg:w-[300px] h-screen bg-white flex-col items-center fixed lg:relative">
                <div class="Name">
                    <div class="flex items-center gap-2">
                        <img src="images/clinic.png" alt="Clinic Logo" class="h-auto"  style="width: auto; max-width: 50px">
                        <span class="text-lg md:text-xl font-semibold text-gray-800">SRCB Clinic</span>
                    </div>
                </div>

                <div class="nav">
                    <div class="text-center pt-[45px] w-full px-4"></div>
                    <div class="flex flex-col space-y-2 ">
                        <!-- Dashboard -->
                        <a href="admin-dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                            <img src="images/dashboard.png" alt="Dashboard icon" class="w-5 h-5">
                            <span class="font-medium">Dashboard</span>
                        </a>

                        <!-- Students -->
                        <a href="admin-students.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-green-50 hover:text-green-600 transition-all duration-200">
                            <img src="images/students.png" alt="Students icon" class="w-5 h-5">
                            <span class="font-medium">Students</span>
                        </a>

                        <!-- Clinic Visits -->
                        <a href="admin-visits.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-all duration-200">
                            <img src="images/clinic-visit.png" alt="Clinic visits icon" class="w-5 h-5">
                            <span class="font-medium">Clinic Visits</span>
                        </a>

                        <!-- Medication -->
                        <a href="admin-medication.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-yellow-50 hover:text-yellow-600 transition-all duration-200">
                            <img src="images/medication.png" alt="Medication icon" class="w-5 h-5">
                            <span class="font-medium">Medication</span>
                        </a>

                        <!-- Appointments -->
                        <a href="admin-appointment.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-red-50 hover:text-red-600 transition-all duration-200">
                            <img src="images/appointments.png" alt="Appointments icon" class="w-5 h-5">
                            <span class="font-medium">Appointments</span>
                        </a>

                        <!-- Reports -->
                        <a href="admin-reports.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-200">
                            <img src="images/reports.png" alt="Reports icon" class="w-5 h-5">
                            <span class="font-medium">Reports</span>
                        </a>
                        <!-- Help Center -->
                         <a href="admin-help.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-pink-50 hover:text-pink-600 transition-all duration-200">
                            <img src="images/reports.png" alt="Reports icon" class="w-5 h-5">
                            <span class="font-medium">Help Center</span>
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
                            <a href="superadmin-account.php" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-50 text-sm text-gray-700 transition-colors">
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
            </div>

            <button class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-md shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <div class="main-context flex-1 h-screen overflow-auto">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 pb-2 pt-[38px] pl-[60px]">Clinic Visits</h2>
                <?php
                include 'visits_data.php';

                // Get filters from query parameters
                $filters = [
                    'search' => $_GET['search'] ?? '',
                    'patient_type' => $_GET['patient_type'] ?? '',
                    'sort' => $_GET['sort'] ?? 'date-desc'
                ];

                // Pagination settings
                $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $rows_per_page = isset($_GET['rows']) ? max(1, intval($_GET['rows'])) : 10;
                $offset = ($current_page - 1) * $rows_per_page;

                // Get data
                $total_visits = getTotalClinicVisitsCount($filters);
                $visits = getAllClinicVisits($filters, $rows_per_page, $offset);
                $total_pages = ceil($total_visits / $rows_per_page);
                ?>

                <div class="visit-info" style="width: 1145px; min-height: 500px; background-color: #ffffff; margin: 0 auto; border-radius: 25px; padding: 20px;">
                    <div class="container mx-auto px-4 py-8">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                            <h2 class="text-xl font-bold text-gray-800">Clinic Visits Information</h2>
                            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                                <form method="GET" class="flex flex-wrap gap-3">
                                    <input type="hidden" name="page" value="1">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        placeholder="Search visits..." 
                                        value="<?php echo htmlspecialchars($filters['search']); ?>"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-40"
                                    >
                                    <select name="patient_type" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                                        <option value="">All Patients</option>
                                        <option value="Student" <?php echo $filters['patient_type'] === 'Student' ? 'selected' : ''; ?>>Students</option>
                                        <option value="Staff" <?php echo $filters['patient_type'] === 'Staff' ? 'selected' : ''; ?>>Staff</option>
                                        <option value="Other" <?php echo $filters['patient_type'] === 'Other' ? 'selected' : ''; ?>>Others</option>
                                    </select>
                                    <select name="sort" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                                        <option value="date-desc" <?php echo $filters['sort'] === 'date-desc' ? 'selected' : ''; ?>>Newest First</option>
                                        <option value="date-asc" <?php echo $filters['sort'] === 'date-asc' ? 'selected' : ''; ?>>Oldest First</option>
                                    </select>
                                    <select name="rows" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                                        <option value="5" <?php echo $rows_per_page == 5 ? 'selected' : ''; ?>>5 rows</option>
                                        <option value="10" <?php echo $rows_per_page == 10 ? 'selected' : ''; ?>>10 rows</option>
                                        <option value="20" <?php echo $rows_per_page == 20 ? 'selected' : ''; ?>>20 rows</option>
                                        <option value="50" <?php echo $rows_per_page == 50 ? 'selected' : ''; ?>>50 rows</option>
                                    </select>
                                </form>
                                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm" onclick="openAddVisitModal()">
                                    Add New Visit
                                </button>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow overflow-hidden mx-auto">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 mx-auto">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Patient Name
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Patient ID
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Grade Level
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Reason
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Treatment
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dispensed Medications
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php if (!empty($visits)): ?>
                                            <?php foreach ($visits as $visit): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars(getPatientName($visit)); ?>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 text-center"><?php echo $visit['patient_id']; ?></div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    <?php echo htmlspecialchars(getPatientInfo($visit)); ?>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    <?php echo htmlspecialchars($visit['reason']); ?>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    <?php echo htmlspecialchars($visit['treatment']); ?>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    None
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <div class="flex justify-center space-x-2">
                                                        <button class="text-blue-600 hover:text-blue-900" title="Edit" onclick="handleVisitAction('edit', <?php echo $visit['visit_id']; ?>)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                        </button>
                                                        <button class="text-red-600 hover:text-red-900" title="Delete" onclick="handleVisitAction('delete', <?php echo $visit['visit_id']; ?>)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <button class="text-green-600 hover:text-green-900" title="View Details" onclick="handleVisitAction('view', <?php echo $visit['visit_id']; ?>)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <button class="text-purple-600 hover:text-purple-900" title="Dispense Medication" onclick="openDispenseModal(<?php echo $visit['visit_id']; ?>)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V7l-7-5z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    No visits found. <?php echo !empty($filters['search']) ? 'Try a different search term.' : ''; ?>
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
                                Showing <?php echo count($visits); ?> of <?php echo $total_visits; ?> visits
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
    
        </div>

        <script src="mobile-nav.js"></script>
        <script src="modal.js"></script>
        <script src="visits.js"></script>
        <script src="medication-dispensing.js"></script>
        <?php include 'edit-visit-modal-updated.html'; ?>
        <?php include 'add-visit-modal.html'; ?>
    </body>
</html>
