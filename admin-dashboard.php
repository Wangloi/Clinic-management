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
                        <table class="w-full border-collapse">
                            <!-- Table Head -->
                            <thead>
                                <tr class="border-b-2 border-t border-gray-500">
                                    <th class="py-2 px-2 text-left text-[10px] md:text-[12px] font-normal">Date/Time</th>
                                    <th class="py-2 px-2 text-left text-[10px] md:text-[12px] font-normal">Student</th>
                                    <th class="py-2 px-2 text-left text-[10px] md:text-[12px] font-normal">Grade</th>
                                    <th class="py-2 px-2 text-left text-[10px] md:text-[12px] font-normal">Reason</th>
                                    <th class="py-2 px-2 text-left text-[10px] md:text-[12px] font-normal">Medication</th>
                                </tr>
                            </thead>
                            
                            <!-- Table Body -->
                            <tbody>
                                <!-- Row 1 -->
                                <tr class="border-b border-gray-300">
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">2023-11-15 08:30</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">Sarah Johnson</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">7</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">Headache</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">Acetaminophen</td>
                                </tr>
                                
                                <!-- Row 2 -->
                                <tr class="border-b border-gray-300">
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">2023-11-15 09:45</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">Michael Chen</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">9</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">Allergy</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">Antihistamine</td>
                                </tr>
                                
                                <!-- Row 3 -->
                                <tr class="border-b border-gray-300">
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">2023-11-15 10:15</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">Emily Rodriguez</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">6</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">Stomach ache</td>
                                    <td class="py-2 px-2 text-[10px] md:text-[11px]">Antacid</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>

                    <div class="side-content">
                        <div class="total-numbers" style="width: 257px; height: 150px; background-color: #ffffff; margin-left: 15px; border-radius: 25px; padding: 20px;">
                            <h4 class="text-[10px] md:text-[16px] text-gray-800 mb-4 pb-2 font-bold">Total Number of Students</h4>  
                        </div>

                        <div class="visit-distribution" style="width: 257px; height: 235px; background-color: #ffffff; margin-left: 15px; margin-top: 15px; border-radius: 25px; padding: 20px;">
                            <h4 class="text-[10px] md:text-[16px] text-gray-800 mb-4 pb-2 font-bold">Visit Distribution by Grade</h4>  
                        </div>
                        
                    </div>
                </div>

                <div class="down-content">
                    <div class="analytics" style="width: 600px; height: 400px; background-color: #ffffff; margin-left: 60px; margin-top: 15px; border-radius: 25px; padding: 20px;">
                        <h5 class="text-[12px] md:text-[18px] text-gray-800 mb-4 pb-2 font-bold">Health Issue Trends</h5>
                    </div>

                    <div class="total-numbers" style="width: 257px; height: 150px; background-color: #ffffff; margin-left: 15px; border-radius: 25px; padding: 20px;">
                        <h4 class="text-[10px] md:text-[16px] text-gray-800 mb-4 pb-2 font-bold">Total Number of Students</h4>  
                    </div>
                </div>
            </div>

    
        </div>

    <script src="mobile-nav.js"></script>
    </body>
</html>

