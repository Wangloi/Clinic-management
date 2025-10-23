<?php include 'connection.php'; ?>
<?php include 'user-role.php'; ?>
<?php include 'verifyer.php'; ?>
<?php include 'admin-students-data.php'; ?>
<?php include 'admin-user-data.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin| Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="../css/admin.css">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link rel="stylesheet" href="../css/modal.css">
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
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 pb-2 pt-[38px] pl-[60px]">Students</h2>

                <div class="student-info" style="width: 1145px; min-height: 500px; background-color: #ffffff; margin: 0 auto; border-radius: 25px; padding: 20px;">
                    <div class="container mx-auto px-4 py-8">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                            <h2 class="text-xl font-bold text-gray-800">Students Information</h2>
                            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                                <form method="GET" class="flex flex-wrap gap-3">
                                    <input type="hidden" name="page" value="1">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        placeholder="Search students..." 
                                        value="<?php echo htmlspecialchars($filters['search']); ?>"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-40"
                                    >
                                    <select name="sort" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                                        <option value="name-asc" <?php echo $filters['sort'] === 'name-asc' ? 'selected' : ''; ?>>Name A-Z</option>
                                        <option value="name-desc" <?php echo $filters['sort'] === 'name-desc' ? 'selected' : ''; ?>>Name Z-A</option>
                                        <option value="visits-asc" <?php echo $filters['sort'] === 'visits-asc' ? 'selected' : ''; ?>>Total Visits Ascending</option>
                                        <option value="visits-desc" <?php echo $filters['sort'] === 'visits-desc' ? 'selected' : ''; ?>>Total Visits Descending</option>
                                    </select>
                                    <select name="department" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                                        <option value="">All Departments</option>
                                        <option value="College" <?php echo $filters['department'] === 'College' ? 'selected' : ''; ?>>College</option>
                                        <option value="SHS" <?php echo $filters['department'] === 'SHS' ? 'selected' : ''; ?>>Senior High</option>
                                        <option value="JHS" <?php echo $filters['department'] === 'JHS' ? 'selected' : ''; ?>>Junior High</option>
                                        <option value="Grade School" <?php echo $filters['department'] === 'Grade School' ? 'selected' : ''; ?>>Grade School</option>
                                    </select>
                                    <select name="rows" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
                                        <option value="5" <?php echo $rows_per_page == 5 ? 'selected' : ''; ?>>5 rows</option>
                                        <option value="10" <?php echo $rows_per_page == 10 ? 'selected' : ''; ?>>10 rows</option>
                                        <option value="20" <?php echo $rows_per_page == 20 ? 'selected' : ''; ?>>20 rows</option>
                                        <option value="50" <?php echo $rows_per_page == 50 ? 'selected' : ''; ?>>50 rows</option>
                                        <option value="100" <?php echo $rows_per_page == 100 ? 'selected' : ''; ?>>100 rows</option>
                                    </select>


                                </form>
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm" onclick="openAddStudentModal()">
                                    Add New Student
                                </button>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow overflow-hidden mx-auto">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 mx-auto">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Student Name
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Student ID
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Program
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Section
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Department
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Total Visits
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php if (!empty($students)): ?>
                                            <?php foreach ($students as $student): ?>
                                            <tr class="hover:bg-gray-50">
                                                <!-- Student Name -->
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars(getStudentFullName($student)); ?>
                                                    </div>
                                                </td>
                                                
                                                <!-- Student ID -->
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 text-center"><?php echo $student['student_id']; ?></div>
                                                </td>
                                                
                                                <!-- Program -->
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    <?php echo htmlspecialchars($student['program_name'] ?? 'N/A'); ?>
                                                </td>
                                                
                                                <!-- Section -->
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    <?php echo htmlspecialchars($student['section_name'] ?? 'N/A'); ?>
                                                </td>
                                                
                                                <!-- Department -->
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    <?php echo htmlspecialchars(getDepartmentDisplayName($student['department_level'] ?? 'N/A')); ?>
                                                </td>
                                                
                                                <!-- Total Visits -->
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                                                    <?php echo $student['total_visits']; ?>
                                                </td>
                                                
                                                <!-- Actions -->
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <div class="flex justify-center space-x-2">
                                                        <button class="text-blue-600 hover:text-blue-900" title="Edit" 
                                                            data-student='<?php echo htmlspecialchars(json_encode($student), ENT_QUOTES, 'UTF-8'); ?>'
                                                            onclick="openEditModal(JSON.parse(this.getAttribute('data-student')))">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                        </button>
                                                        <button class="text-red-600 hover:text-red-900" title="Delete" onclick="deleteStudent('<?php echo htmlspecialchars($student['student_id']); ?>', '<?php echo htmlspecialchars(getStudentFullName($student)); ?>')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <button class="text-green-600 hover:text-green-900" title="View Details" onclick="viewStudentDetails('<?php echo htmlspecialchars($student['student_id']); ?>')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    No students found. <?php echo !empty($filters['search']) ? 'Try a different search term.' : ''; ?>
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
                                Showing <?php echo count($students); ?> of <?php echo $total_students; ?> students
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

                <!-- Edit Student Modal -->
                <div id="editStudentModal" class="fixed inset-0 flex items-center justify-center z-[1000] hidden backdrop-blur-sm">
                    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto relative">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-xl font-semibold text-gray-800">Edit Student Information</h3>
                            <span class="text-gray-400 hover:text-gray-600 cursor-pointer text-2xl" onclick="closeModal('editStudentModal')">&times;</span>
                        </div>

                        <div class="px-6 py-4">
                            <form id="editStudentForm" method="POST" action="update_student.php">
                                <input type="hidden" name="student_id" id="edit_student_id">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="edit_first_name">First Name</label>
                                        <input type="text" id="edit_first_name" name="first_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="edit_middle_name">Middle Name</label>
                                        <input type="text" id="edit_middle_name" name="middle_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="edit_last_name">Last Name</label>
                                        <input type="text" id="edit_last_name" name="last_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="edit_student_id_display">Student ID</label>
                                        <input type="text" id="edit_student_id_display" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" disabled>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="edit_program">Program</label>
                                        <select id="edit_program" name="program" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="loadSections()">
                                            <option value="">Select Program</option>
                                            <?php foreach ($programs as $program): ?>
                                                <option value="<?php echo $program['program_id']; ?>">
                                                    <?php echo htmlspecialchars($program['program_name']); ?> (<?php echo htmlspecialchars($program['department_level']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="edit_section">Section</label>
                                        <select id="edit_section" name="section" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            <option value="">Select Section</option>
                                            <?php foreach ($sections as $section): ?>
                                                <option value="<?php echo $section['section_id']; ?>">
                                                    <?php echo htmlspecialchars($section['section_name']); ?> (<?php echo htmlspecialchars($section['program_name']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1" for="edit_department">Department</label>
                                    <select id="edit_department" name="department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="loadPrograms()">
                                        <option value="">Select Department</option>
                                        <option value="College">College</option>
                                        <option value="SHS">Senior High School</option>
                                        <option value="JHS">Junior High School</option>
                                        <option value="Grade School">Grade School</option>
                                    </select>
                                </div>
                            </form>
                        </div>

                        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('editStudentModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" form="editStudentForm" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>


                <!-- Student Details Modal -->
                <div id="studentDetailsModal" class="fixed inset-0 flex items-center justify-center z-[2000] hidden backdrop-blur-sm">
                    <div class="bg-white rounded-lg shadow-2xl w-full max-w-6xl mx-4 max-h-[90vh] overflow-y-auto relative">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-xl font-semibold text-gray-800">Student Details & Health Records</h3>
                            <span class="text-gray-400 hover:text-gray-600 cursor-pointer text-2xl" onclick="closeModal('studentDetailsModal')">&times;</span>
                        </div>

                        <div class="px-6 py-4">
                            <!-- Loading State -->
                            <div id="studentDetailsLoading" class="text-center py-8">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <p class="mt-2 text-gray-600">Loading student details...</p>
                            </div>

                            <!-- Student Details Content -->
                            <div id="studentDetailsContent" class="hidden">
                                <!-- Student Basic Info -->
                                <div class="bg-blue-50 p-6 rounded-lg mb-6">
                                    <h4 class="text-lg font-semibold text-blue-800 mb-4">👤 Student Information</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Full Name</label>
                                            <p id="studentFullName" class="text-lg font-semibold text-gray-800">-</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Student ID</label>
                                            <p id="studentIdDisplay" class="text-lg font-semibold text-gray-800">-</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Contact Number</label>
                                            <p id="studentContact" class="text-lg font-semibold text-gray-800">-</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Section</label>
                                            <p id="studentSection" class="text-lg font-semibold text-gray-800">-</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Program</label>
                                            <p id="studentProgram" class="text-lg font-semibold text-gray-800">-</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Department</label>
                                            <p id="studentDepartment" class="text-lg font-semibold text-gray-800">-</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Health Records Section -->
                                <div class="bg-green-50 p-6 rounded-lg">
                                    <h4 class="text-lg font-semibold text-green-800 mb-4">🏥 Health Records</h4>
                                    
                                    <!-- No Health Record State -->
                                    <div id="noHealthRecord" class="text-center py-8 hidden">
                                        <div class="text-6xl mb-4">📋</div>
                                        <h5 class="text-lg font-medium text-gray-700 mb-2">No Health Record Found</h5>
                                        <p class="text-gray-600">This student has not completed their health questionnaire yet.</p>
                                    </div>

                                    <!-- Health Record Content -->
                                    <div id="healthRecordContent" class="hidden">
                                        <!-- Personal Health Info -->
                                        <div class="mb-6">
                                            <h5 class="text-md font-semibold text-gray-700 mb-3 border-b border-gray-300 pb-1">Personal Health Information</h5>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Sex</label>
                                                    <p id="healthSex" class="text-sm text-gray-800">-</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Birthday</label>
                                                    <p id="healthBirthday" class="text-sm text-gray-800">-</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Age</label>
                                                    <p id="healthAge" class="text-sm text-gray-800">-</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Education Level</label>
                                                    <p id="healthEducationLevel" class="text-sm text-gray-800">-</p>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <label class="block text-sm font-medium text-gray-600">Home Address</label>
                                                <p id="healthAddress" class="text-sm text-gray-800">-</p>
                                            </div>
                                        </div>

                                        <!-- Vital Signs -->
                                        <div class="mb-6">
                                            <h5 class="text-md font-semibold text-gray-700 mb-3 border-b border-gray-300 pb-1">Vital Signs</h5>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Height</label>
                                                    <p id="healthHeight" class="text-sm text-gray-800">-</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Weight</label>
                                                    <p id="healthWeight" class="text-sm text-gray-800">-</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Blood Pressure</label>
                                                    <p id="healthBP" class="text-sm text-gray-800">-</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Heart Rate</label>
                                                    <p id="healthHR" class="text-sm text-gray-800">-</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Temperature</label>
                                                    <p id="healthTemp" class="text-sm text-gray-800">-</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Health Conditions -->
                                        <div class="mb-6">
                                            <h5 class="text-md font-semibold text-gray-700 mb-3 border-b border-gray-300 pb-1">Health Conditions</h5>
                                            <div class="space-y-3">
                                                <div id="allergiesInfo" class="hidden">
                                                    <div class="bg-red-100 p-3 rounded">
                                                        <strong class="text-red-800">⚠️ Allergies:</strong>
                                                        <span id="allergiesRemarks" class="text-red-700"></span>
                                                    </div>
                                                </div>
                                                <div id="asthmaInfo" class="hidden">
                                                    <div class="bg-orange-100 p-3 rounded">
                                                        <strong class="text-orange-800">🫁 Asthma:</strong>
                                                        <span id="asthmaRemarks" class="text-orange-700"></span>
                                                    </div>
                                                </div>
                                                <div id="healthProblemsInfo" class="hidden">
                                                    <div class="bg-yellow-100 p-3 rounded">
                                                        <strong class="text-yellow-800">🏥 Health Problems:</strong>
                                                        <span id="healthProblemsRemarks" class="text-yellow-700"></span>
                                                    </div>
                                                </div>
                                                <div id="noHealthConditions" class="text-green-600 font-medium">✅ No significant health conditions reported</div>
                                            </div>
                                        </div>

                                        <!-- Medications & Notes -->
                                        <div class="mb-6">
                                            <h5 class="text-md font-semibold text-gray-700 mb-3 border-b border-gray-300 pb-1">Current Medications & Notes</h5>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Current Medications/Vitamins</label>
                                                    <p id="healthMedications" class="text-sm text-gray-800 bg-gray-50 p-2 rounded">-</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600">Additional Notes</label>
                                                    <p id="healthNotes" class="text-sm text-gray-800 bg-gray-50 p-2 rounded">-</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Record Info -->
                                        <div class="bg-gray-100 p-4 rounded-lg">
                                            <h6 class="font-semibold text-gray-700 mb-2">Record Information</h6>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                                <p><strong>Submitted:</strong> <span id="healthSubmitted">-</span></p>
                                                <p><strong>Status:</strong> <span id="healthStatus">-</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                            <button type="button" onclick="closeModal('studentDetailsModal')" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Student Modal -->
                <div id="addStudentModal" class="fixed inset-0 flex items-center justify-center z-[1500] hidden backdrop-blur-sm">
                    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto relative">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-800">Add New Student</h3>
                            <span class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 cursor-pointer text-2xl" onclick="closeModal('addStudentModal')">&times;</span>
                        </div>

                        <div class="px-6 py-4">
                            <form id="addStudentForm" method="POST" action="add_student.php">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="add_first_name">First Name</label>
                                        <input type="text" id="add_first_name" name="first_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="add_middle_name">Middle Name</label>
                                        <input type="text" id="add_middle_name" name="middle_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="add_last_name">Last Name</label>
                                        <input type="text" id="add_last_name" name="last_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="add_student_id">Student ID</label>
                                        <input type="text" id="add_student_id" name="student_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" pattern="[a-zA-Z0-9\-_]+" title="Letters, numbers, hyphens, and underscores only" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1" for="add_contact_number">Contact Number</label>
                                    <input type="tel" id="add_contact_number" name="contact_number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 09123456789" pattern="[0-9]+" title="Please enter only numbers" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="add_department">Department</label>
                                        <select id="add_department" name="department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="loadPrograms()">
                                            <option value="">Select Department</option>
                                            <option value="College">College</option>
                                            <option value="SHS">Senior High School</option>
                                            <option value="JHS">Junior High School</option>
                                            <option value="Grade School">Grade School</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="add_program">Program</label>
                                        <select id="add_program" name="program" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="loadSections()">
                                            <option value="">Select Program</option>
                                            <?php foreach ($programs as $program): ?>
                                                <option value="<?php echo $program['program_id']; ?>">
                                                    <?php echo htmlspecialchars($program['program_name']); ?> (<?php echo htmlspecialchars($program['department_level']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1" for="add_section">Section</label>
                                    <select id="add_section" name="section" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Select Section</option>
                                        <?php foreach ($sections as $section): ?>
                                            <option value="<?php echo $section['section_id']; ?>">
                                                <?php echo htmlspecialchars($section['section_name']); ?> (<?php echo htmlspecialchars($section['program_name']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>
                        </div>

                        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('addStudentModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" form="addStudentForm" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Add Student
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script>
        var allPrograms = <?php echo json_encode($programs); ?>;
        var allSections = <?php echo json_encode($sections); ?>;
    </script>
    <script src="../modal.js"></script>
    <script src="../students-management.js"></script>
    <script src="../logout.js"></script>

    <?php include 'admin-profile-modals.php'; ?>
</body>
</html>
