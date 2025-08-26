<?php include 'user-role.php'; ?>
<?php include 'verifyer.php'; ?>
<?php include 'student_count.php';
 ?>
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
                </div>
            </div>

            <button class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-md shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <div class="main-context flex-1 h-screen overflow-auto">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 pb-2 pt-[38px] pl-[60px]">Dashboard</h2>
                <div class="content">
                <div class="table" style="width: 600px; height: 400px; background-color: #ffffff; margin-left: 60px; border-radius: 25px; padding: 20px;">
                    <h3 class="text-[12px] md:text-[18px] text-gray-800 mb-4 pb-2 font-bold">Recent Clinic Visits</h3>
                    <div class="overflow-auto" style="height: calc(100% - 40px);">
                        <?php if (!empty($recentVisits)): ?>
                            <table class="w-full border-collapse">
                                <!-- Table Head -->
                                <thead>
                                    <tr class="border-b-2 border-t border-gray-500">
                                        <th class="py-2 px-2 text-left text-[10px] md:text-[12px] font-normal">Date</th>
                                        <th class="py-2 px-2 text-left text-[10px] md:text-[12px] font-normal">Patient</th>
                                        <th class="py-2 px-2 text-left text-[10px] md:text-[12px] font-normal">Type</th>
                                        <th class="py-2 px-2 text-left text-[10px] md:text-[12px] font-normal">Reason</th>
                                        <th class="py-2 px-2 text-left text-[10px] md:text-[12px] font-normal">Treatment</th>
                                    </tr>
                                </thead>
                                
                                <!-- Table Body -->
                                <tbody>
                                    <?php foreach ($recentVisits as $visit): ?>
                                    <tr class="border-b border-gray-300 hover:bg-gray-50">
                                        <!-- Date -->
                                        <td class="py-2 px-2 text-[10px] md:text-[11px]">
                                            <?php echo date('M j, Y', strtotime($visit['visit_date'])); ?>
                                        </td>
                                        
                                        <!-- Patient Name -->
                                        <td class="py-2 px-2 text-[10px] md:text-[11px] font-medium">
                                            <?php echo htmlspecialchars(getPatientName($visit)); ?>
                                        </td>
                                        
                                        <!-- Type/Grade/Section -->
                                        <td class="py-2 px-2 text-[10px] md:text-[11px]">
                                            <?php echo htmlspecialchars(getPatientInfo($visit)); ?>
                                        </td>
                                        
                                        <!-- Reason -->
                                        <td class="py-2 px-2 text-[10px] md:text-[11px]">
                                            <?php echo !empty($visit['reason']) ? htmlspecialchars(substr($visit['reason'], 0, 30)) . (strlen($visit['reason']) > 30 ? '...' : '') : 'N/A'; ?>
                                        </td>
                                        
                                        <!-- Treatment -->
                                        <td class="py-2 px-2 text-[10px] md:text-[11px]">
                                            <?php echo !empty($visit['treatment']) ? htmlspecialchars(substr($visit['treatment'], 0, 30)) . (strlen($visit['treatment']) > 30 ? '...' : '') : 'N/A'; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-500 text-sm">
                                No recent clinic visits found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                    <div class="side-content">
                        <div class="total-numbers" style="width: 257px; height: 150px; background-color: #ffffff; margin-left: 15px; border-radius: 25px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h4 class="text-[10px] md:text-[16px] text-gray-800 mb-2 font-bold">Total Students</h4>  
                            <div class="student-count text-3xl font-bold text-blue-600 text-center my-3">
                                <?php echo getStudentCount(); ?>
                            </div>
                            <p class="text-xs text-gray-500 text-center">All registered students</p>
                        </div>

                        <div class="visit-distribution" style="width: 257px; height: 235px; background-color: #ffffff; margin-left: 15px; margin-top: 15px; border-radius: 25px; padding: 20px;">
                            <h4 class="text-[10px] md:text-[16px] text-gray-800 mb-4 pb-2 font-bold">Visit Distribution by Grade</h4>  
                        </div>
                    </div>

                    <div class="stats" style="width: 257px; height: 400px; background-color: #ffffff; margin-left: 15px; border-radius: 25px; padding: 20px;">
                            <h4 class="text-[10px] md:text-[16px] text-gray-800 font-bold">Medication Usage Stats</h4>
                            <h4 class="text-[6px] md:text-[12px] text-gray-800 mb-4 pb-2 font-medium">00/00/00</h4>
                    </div>

                </div>

                <div class="down-content">
                    <div class="analytics" style="width: 1145px; height: 400px; background-color: #ffffff; margin-left: 60px; margin-top: 15px; border-radius: 25px; padding: 20px;">
                        <h5 class="text-[12px] md:text-[18px] text-gray-800 mb-4 pb-2 font-bold">Health Issue Trends</h5>
                    </div>

                </div>
            </div>

    
        </div>

    <script src="mobile-nav.js"></script>
    </body>
</html>