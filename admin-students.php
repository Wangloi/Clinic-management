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
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 pb-2 pt-[38px] pl-[60px]">Students</h2>
                <div class="student-info" style="width: 1145px; height: 500px; background-color: #ffffff; margin-left: 60px; border-radius: 25px; padding: 20px;">
                    <div class="container mx-auto px-4 py-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <h2 class="text-xl font-bold text-gray-800">Students Information</h2>
                        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <input 
                            type="text" 
                            placeholder="Search students..." 
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        >
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                            Add New Student
                        </button>
                        <select id="sortFilter" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="name-asc">ASC</option>
                        <option value="name-desc">DESC</option>
                        <option value="id-asc">ID</option>
                        </select>
                        <select id="sortFilter" class="text-[12px] md:text-[14px] px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="gs">Grade School</option>
                        <option value="jhs">Junior High School</option>
                        <option value="shs">Senior High School</option>
                        <option value="college">College</option>
                        </select>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                Student Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                Student ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                Year Level
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                Section
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                Department
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                Total Visits
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Student 1 -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">John Doe</div>
                                    </div>
                                </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">2023-001</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    3rd Year
                                </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">BSIT 3</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">HED</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">12</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <button class="text-blue-600 hover:text-blue-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    </button>
                                    <button class="text-gray-600 hover:text-gray-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414L11.414 12l3.293 3.293a1 1 0 01-1.414 1.414L10 13.414l-3.293 3.293a1 1 0 01-1.414-1.414L8.586 12 5.293 8.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    </button>
                                </div>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Maria Santos</div>
                                    </div>
                                </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2023-002</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    2nd Year
                                </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">B</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Information Technology</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">8</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <!-- Action buttons same as above -->
                                </td>
                            </tr>

                            <!-- Student 3 -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Carlos Reyes</div>
                                    </div>
                                </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2023-003</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    1st Year
                                </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">C</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Engineering</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">5</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <!-- Action buttons -->
                                </td>
                            </tr>

                            </tbody>
                        </table>
                        </div>
                    </div>
                    </div>

                </div>
            </div>
    
        </div>

    <script src="mobile-nav.js"></script>
    <script src="searchbar.js"></script>
    <script src="filterbar.js"></script>
    </body>
</html>