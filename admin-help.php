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
                        <div class="flex items-center space-x-3 mb-2 cursor-pointer" onclick="openModal('profileModal')">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-1">
                            <a href="logout.php" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-red-50 text-sm text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>

                    <!-- Profile Modal -->
                    <div id="profileModal" class="fixed inset-0 z-[9999] hidden bg-black/50 backdrop-blur-lg">
                        <div class="flex items-center justify-center min-h-screen">
                            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 p-6 relative z-[10000] pointer-events-auto">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold text-gray-800">Your Profile</h3>
                                    <button onclick="closeModal('profileModal')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                                </div>
                                <div class="space-y-3 text-gray-700">
                                    <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                    <p><strong>Role:</strong> Administrator</p>
                                    <!-- Add more personal information fields here as needed -->
                                </div>
                            </div>
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
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 pb-2 pt-[38px] pl-[60px]">Help Center</h2>
                <div class="Recent p-6 bg-gray-50 min-h-full">
                    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Welcome to the Help Center</h3>
                        <p class="text-gray-600 mb-6">This guide will help you navigate and use the Integrated Digital Clinic Management System of St. Rita's College of Balingasag effectively. Below, you'll find detailed instructions for each section of the system.</p>

                        <div class="space-y-8">
                            <!-- Dashboard -->
                            <div class="border-b pb-4">
                                <h4 class="text-lg font-medium text-blue-600 mb-2">Dashboard</h4>
                                <p class="text-gray-700 mb-2">The Dashboard provides an overview of key metrics and recent activities in the clinic system.</p>
                                <ul class="list-disc list-inside text-gray-600 space-y-1">
                                    <li>View total student count, clinic visits, and medication inventory at a glance.</li>
                                    <li>Monitor recent clinic visits and upcoming appointments.</li>
                                    <li>Access quick links to frequently used sections.</li>
                                    <li>Review system statistics and trends.</li>
                                </ul>
                            </div>

                            <!-- Students -->
                            <div class="border-b pb-4">
                                <h4 class="text-lg font-medium text-green-600 mb-2">Students Management</h4>
                                <p class="text-gray-700 mb-2">Manage student information and records.</p>
                                <ul class="list-disc list-inside text-gray-600 space-y-1">
                                    <li>Add new students with their personal details, program, and section.</li>
                                    <li>Search and filter students by name, program, or section.</li>
                                    <li>Edit student information as needed.</li>
                                    <li>View student visit history and health records.</li>
                                    <li>Delete student records (use with caution).</li>
                                </ul>
                            </div>

                            <!-- Clinic Visits -->
                            <div class="border-b pb-4">
                                <h4 class="text-lg font-medium text-purple-600 mb-2">Clinic Visits</h4>
                                <p class="text-gray-700 mb-2">Record and manage clinic visits and consultations.</p>
                                <ul class="list-disc list-inside text-gray-600 space-y-1">
                                    <li>Log new clinic visits with student details, symptoms, and diagnosis.</li>
                                    <li>Record vital signs and health assessments.</li>
                                    <li>Track visit reasons and health issues.</li>
                                    <li>Update visit information and add follow-up notes.</li>
                                    <li>View visit history and search past consultations.</li>
                                </ul>
                            </div>

                            <!-- Medication -->
                            <div class="border-b pb-4">
                                <h4 class="text-lg font-medium text-yellow-600 mb-2">Medication Management</h4>
                                <p class="text-gray-700 mb-2">Manage clinic medication inventory and dispensing.</p>
                                <ul class="list-disc list-inside text-gray-600 space-y-1">
                                    <li>Add new medications to the inventory with details like name, category, and stock levels.</li>
                                    <li>Monitor low stock alerts and reorder medications.</li>
                                    <li>Dispense medications during clinic visits.</li>
                                    <li>Track medication usage and prescription history.</li>
                                    <li>Edit medication details and update stock quantities.</li>
                                </ul>
                            </div>

                            <!-- Appointments -->
                            <div class="border-b pb-4">
                                <h4 class="text-lg font-medium text-red-600 mb-2">Appointments</h4>
                                <p class="text-gray-700 mb-2">Schedule and manage clinic appointments.</p>
                                <ul class="list-disc list-inside text-gray-600 space-y-1">
                                    <li>Schedule new appointments for students.</li>
                                    <li>View upcoming and past appointments.</li>
                                    <li>Reschedule or cancel appointments as needed.</li>
                                    <li>Send appointment reminders (if notification system is enabled).</li>
                                    <li>Filter appointments by date, student, or status.</li>
                                </ul>
                            </div>

                            <!-- Reports -->
                            <div class="border-b pb-4">
                                <h4 class="text-lg font-medium text-indigo-600 mb-2">Reports</h4>
                                <p class="text-gray-700 mb-2">Generate and view various clinic reports.</p>
                                <ul class="list-disc list-inside text-gray-600 space-y-1">
                                    <li>Generate monthly visit reports and trends.</li>
                                    <li>View medication usage and dispensing statistics.</li>
                                    <li>Access student health records and visit summaries.</li>
                                    <li>Export reports in PDF or Excel format.</li>
                                    <li>Analyze clinic performance metrics and KPIs.</li>
                                </ul>
                            </div>

                            <!-- General Tips -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-800 mb-2">General Tips</h4>
                                <ul class="list-disc list-inside text-gray-600 space-y-1">
                                    <li>Use the search and filter functions to quickly find information.</li>
                                    <li>Regularly check the Dashboard for system updates and alerts.</li>
                                    <li>Ensure all data entered is accurate and up-to-date.</li>
                                    <li>Log out of the system when not in use for security purposes.</li>
                                    <li>Contact the system administrator if you encounter any issues.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                            <h5 class="font-medium text-blue-800 mb-2">Need More Help?</h5>
                            <p class="text-blue-700">If you have questions or need assistance with specific features, please contact the system administrator or refer to the user manual for detailed instructions.</p>
                        </div>
                    </div>
                </div>
            </div>
    
        </div>

    <script src="mobile-nav.js"></script>
    </body>
</html>