<?php include 'connection.php'; ?>
<?php include 'user-role.php'; ?>
<?php include 'verifyer.php'; ?>
<?php include 'student_count.php'; ?>
<?php include 'recent_visits.php'; ?>
<?php $recentVisits = getRecentClinicVisits(10); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin| Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="admin.css">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 pb-2 pt-[38px] pl-[60px]">Dashboard</h2>
                <div class="content">
                <div class="table" style="width: 600px; height: 400px; background-color: #ffffff; margin-left: 60px; border-radius: 25px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3 class="text-[12px] md:text-[18px] text-gray-800 mb-4 pb-2 font-bold">Recent Clinic Visits</h3>
                    <div class="overflow-auto" style="height: calc(100% - 40px);">
                        <?php if (!empty($recentVisits)): ?>
                            <table class="w-full border-collapse">
                                <!-- Table Head -->
                                <thead>
                                    <tr class="border-b-2 border-t border-gray-300 bg-gray-50">
                                        <th class="py-3 px-3 text-left text-[10px] md:text-[12px] font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                        <th class="py-3 px-3 text-left text-[10px] md:text-[12px] font-semibold text-gray-700 uppercase tracking-wider">Patient</th>
                                        <th class="py-3 px-3 text-left text-[10px] md:text-[12px] font-semibold text-gray-700 uppercase tracking-wider">Program</th>
                                        <th class="py-3 px-3 text-left text-[10px] md:text-[12px] font-semibold text-gray-700 uppercase tracking-wider">Reason</th>
                                        <th class="py-3 px-3 text-left text-[10px] md:text-[12px] font-semibold text-gray-700 uppercase tracking-wider">Treatment</th>
                                    </tr>
                                </thead>

                                <!-- Table Body -->
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($recentVisits as $visit): ?>
                                    <tr class="hover:bg-blue-50 transition-colors duration-150">
                                        <!-- Date -->
                                        <td class="py-3 px-3 text-[10px] md:text-[11px] text-gray-900">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                                <?php echo date('M j, Y', strtotime($visit['visit_date'])); ?>
                                            </div>
                                        </td>

                                        <!-- Patient Name -->
                                        <td class="py-3 px-3 text-[10px] md:text-[11px] font-medium text-gray-900">
                                            <?php echo htmlspecialchars(getPatientName($visit)); ?>
                                        </td>

                                        <!-- Program -->
                                        <td class="py-3 px-3 text-[10px] md:text-[11px]">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                <?php echo htmlspecialchars(getPatientInfo($visit)); ?>
                                            </span>
                                        </td>

                                        <!-- Reason -->
                                        <td class="py-3 px-3 text-[10px] md:text-[11px] text-gray-700">
                                            <?php echo !empty($visit['reason']) ? htmlspecialchars(substr($visit['reason'], 0, 30)) . (strlen($visit['reason']) > 30 ? '...' : '') : '<span class="text-gray-400">N/A</span>'; ?>
                                        </td>

                                        <!-- Treatment -->
                                        <td class="py-3 px-3 text-[10px] md:text-[11px] text-gray-700">
                                            <?php echo !empty($visit['treatment']) ? htmlspecialchars(substr($visit['treatment'], 0, 30)) . (strlen($visit['treatment']) > 30 ? '...' : '') : '<span class="text-gray-400">N/A</span>'; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">No recent clinic visits found</p>
                                <p class="text-gray-400 text-xs mt-1">Check back later for new visits</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                    <div class="side-content">
                        <div class="total-numbers" style="width: 257px; height: 150px; background-color: #ffffff; margin-left: 15px; border-radius: 25px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h4 class="text-[10px] md:text-[16px] text-gray-800 mb-2 font-bold">Total Students</h4>
                            <div class="student-count text-3xl font-bold text-blue-600 text-center my-3 cursor-pointer hover:text-blue-800 transition-colors" onclick="showStudentDetails()">
                                <?php echo getStudentCount(); ?>
                            </div>
                            <p class="text-xs text-gray-500 text-center">All registered students</p>
                        </div>

                        <div class="visit-distribution" style="width: 257px; height: 235px; background-color: #ffffff; margin-left: 15px; margin-top: 15px; border-radius: 25px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h4 class="text-[10px] md:text-[16px] text-gray-800 mb-4 pb-2 font-bold">Visit Distribution by Grade</h4>
                            <div class="space-y-2">
                                <?php
                                $distribution = getVisitDistributionByGrade();
                                // Debug: print the distribution array
                                // echo '<pre>' . print_r($distribution, true) . '</pre>';
                                if (!empty($distribution)) {
                                    $colors = ['bg-blue-100 text-blue-800', 'bg-green-100 text-green-800', 'bg-yellow-100 text-yellow-800', 'bg-purple-100 text-purple-800', 'bg-pink-100 text-pink-800', 'bg-indigo-100 text-indigo-800'];
                                    $colorIndex = 0;
                                    foreach ($distribution as $dist) {
                                        $colorClass = $colors[$colorIndex % count($colors)];
                                        echo '<div class="flex justify-between items-center p-2 rounded-lg ' . $colorClass . '">';
                                        echo '<span class="text-[10px] md:text-[12px] font-medium">' . htmlspecialchars($dist['department_level']) . '</span>';
                                        echo '<span class="text-[10px] md:text-[12px] font-bold">' . intval($dist['visit_count']) . '</span>';
                                        echo '</div>';
                                        $colorIndex++;
                                    }
                                } else {
                                    echo '<div class="text-center py-4 text-gray-500 text-sm">No data available</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="stats" style="width: 257px; height: 400px; background-color: #ffffff; margin-left: 15px; border-radius: 25px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <h4 class="text-[10px] md:text-[16px] text-gray-800 mb-4 pb-2 font-bold">Medication Usage Stats</h4>

                        <!-- Current Date -->
                        <div class="mb-4">
                            <p class="text-[8px] md:text-[10px] text-gray-500 uppercase tracking-wider">Current Period</p>
                            <p class="text-[10px] md:text-[12px] text-gray-800 font-medium"><?php echo date('M d, Y'); ?></p>
                        </div>

                        <!-- Stats Grid -->
                        <div class="space-y-3">
                        <!-- Most Used Medication -->
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[8px] md:text-[10px] text-blue-600 uppercase tracking-wider">Most Used</p>
                                    <p id="most-used-med" class="text-[10px] md:text-[12px] text-gray-800 font-medium">Loading...</p>
                                </div>
                                <div class="text-right">
                                    <p id="total-doses" class="text-[12px] md:text-[14px] text-blue-600 font-bold">0</p>
                                    <p class="text-[6px] md:text-[8px] text-gray-500">doses</p>
                                </div>
                            </div>
                        </div>

                            <!-- Total Prescriptions -->
                            <div class="bg-green-50 p-3 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[8px] md:text-[10px] text-green-600 uppercase tracking-wider">Total Rx</p>
                                        <p class="text-[10px] md:text-[12px] text-gray-800 font-medium">This Week</p>
                                    </div>
                                    <div class="text-right">
                                        <p id="weekly-prescriptions" class="text-[12px] md:text-[14px] text-green-600 font-bold">0</p>
                                        <p class="text-[6px] md:text-[8px] text-gray-500">prescriptions</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Low Stock Alert -->
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[8px] md:text-[10px] text-yellow-600 uppercase tracking-wider">Low Stock</p>
                                        <p class="text-[10px] md:text-[12px] text-gray-800 font-medium">Alert</p>
                                    </div>
                                    <div class="text-right">
                                        <p id="low-stock-count" class="text-[12px] md:text-[14px] text-yellow-600 font-bold">0</p>
                                        <p class="text-[6px] md:text-[8px] text-gray-500">items</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Usage -->
                            <div class="bg-purple-50 p-3 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[8px] md:text-[10px] text-purple-600 uppercase tracking-wider">Monthly</p>
                                        <p class="text-[10px] md:text-[12px] text-gray-800 font-medium">Usage</p>
                                    </div>
                                    <div class="text-right">
                                        <p id="monthly-usage" class="text-[12px] md:text-[14px] text-purple-600 font-bold">0</p>
                                        <p class="text-[6px] md:text-[8px] text-gray-500">doses</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 pt-3 border-t border-gray-200">
                            <p class="text-[6px] md:text-[8px] text-gray-500 text-center">Last updated: <span id="last-updated"><?php echo date('H:i'); ?></span></p>
                        </div>
                    </div>

                </div>

                <div class="down-content">
                    <div class="analytics" style="width: 1145px; height: 100vh; background-color: #ffffff; margin-left: 60px; margin-top: 15px; border-radius: 25px; padding: 20px;">
                        <h5 class="text-[12px] md:text-[18px] text-gray-800 mb-4 pb-2 font-bold">Health Issue Trends Analytics</h5>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Single Bar Chart Container -->
                            <div class="col-span-3 bg-gray-50 rounded-lg p-6">
                                <div id="chart-container">
                                    <!-- Chart will be rendered here by JavaScript -->
                                    <div class="text-center py-12">
                                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 text-lg font-medium">Loading Chart Data...</p>
                                        <p class="text-gray-400 text-sm mt-1">Please wait while we fetch the latest visit statistics</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Stats -->
                        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php
                            try {
                                // Total visits this month
                                $stmt = $pdo->query("SELECT COUNT(*) as total_visits FROM clinic_visits WHERE MONTH(visit_date) = MONTH(CURRENT_DATE()) AND YEAR(visit_date) = YEAR(CURRENT_DATE())");
                                $monthlyVisits = $stmt->fetchColumn();

                                // Most common diagnosis this month
                                $stmt = $pdo->query("
                                    SELECT diagnosis, COUNT(*) as diag_count
                                    FROM clinic_visits
                                    WHERE MONTH(visit_date) = MONTH(CURRENT_DATE()) AND YEAR(visit_date) = YEAR(CURRENT_DATE())
                                    AND diagnosis IS NOT NULL AND diagnosis != ''
                                    GROUP BY diagnosis
                                    ORDER BY diag_count DESC
                                    LIMIT 1
                                ");
                                $commonDiagnosis = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Average visits per week
                                $stmt = $pdo->query("
                                    SELECT ROUND(AVG(weekly_count)) as avg_weekly
                                    FROM (
                                        SELECT COUNT(*) as weekly_count
                                        FROM clinic_visits
                                        WHERE visit_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 4 WEEK)
                                        GROUP BY YEAR(visit_date), WEEK(visit_date)
                                    ) as weekly_stats
                                ");
                                $avgWeekly = $stmt->fetchColumn();

                                echo '<div class="bg-blue-50 p-3 rounded-lg text-center">';
                                echo '<p class="text-xs text-blue-600 uppercase tracking-wider">This Month</p>';
                                echo '<p class="text-lg font-bold text-blue-800">' . intval($monthlyVisits) . '</p>';
                                echo '<p class="text-xs text-gray-500">Total Visits</p>';
                                echo '</div>';

                                echo '<div class="bg-green-50 p-3 rounded-lg text-center">';
                                echo '<p class="text-xs text-green-600 uppercase tracking-wider">Most Common</p>';
                                echo '<p class="text-sm font-bold text-green-800">' . ($commonDiagnosis ? htmlspecialchars(substr($commonDiagnosis['diagnosis'], 0, 15)) . (strlen($commonDiagnosis['diagnosis']) > 15 ? '...' : '') : 'N/A') . '</p>';
                                echo '<p class="text-xs text-gray-500">Diagnosis</p>';
                                echo '</div>';

                                echo '<div class="bg-purple-50 p-3 rounded-lg text-center">';
                                echo '<p class="text-xs text-purple-600 uppercase tracking-wider">Weekly Avg</p>';
                                echo '<p class="text-lg font-bold text-purple-800">' . intval($avgWeekly) . '</p>';
                                echo '<p class="text-xs text-gray-500">Visits</p>';
                                echo '</div>';

                                echo '<div class="bg-orange-50 p-3 rounded-lg text-center">';
                                echo '<p class="text-xs text-orange-600 uppercase tracking-wider">Trend</p>';
                                echo '<p class="text-lg font-bold text-orange-800">' . ($monthlyVisits > $avgWeekly * 4 ? '↗️ High' : ($monthlyVisits < $avgWeekly * 4 ? '↘️ Low' : '➡️ Normal')) . '</p>';
                                echo '<p class="text-xs text-gray-500">Activity</p>';
                                echo '</div>';

                            } catch (PDOException $e) {
                                echo '<div class="col-span-4 text-center py-4 text-gray-500 text-sm">Unable to load summary statistics</div>';
                            }
                            ?>
                        </div>
                    </div>

                </div>
            </div>

    
        </div>

    <script src="mobile-nav.js"></script>

    <!-- Chart.js Analytics Scripts -->
    <script>
        // Health Issue Trends Analytics - Single Bar Chart with Auto-Refresh
        let analyticsChart = null;
        let autoRefreshInterval = null;
        const REFRESH_INTERVAL = 30000; // 30 seconds

        document.addEventListener('DOMContentLoaded', function() {
            // Initial data fetch
            fetchVisitReasonsData();

            // Start auto-refresh
            startAutoRefresh();
        });

        function startAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
            autoRefreshInterval = setInterval(() => {
                fetchVisitReasonsData();
            }, REFRESH_INTERVAL);
        }



        function fetchVisitReasonsData() {
            fetch('get_visit_reasons_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.reasons.length > 0) {
                        createVisitReasonsBarChart(data.reasons);
                    } else {
                        showNoDataMessage();
                    }
                })
                .catch(error => {
                    console.log('Error fetching visit reasons data:', error);
                    showNoDataMessage();
                });
        }

        // Fetch medication usage stats and update the dashboard
        function fetchMedicationUsageData() {
            fetch('get_medication_usage_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.medications.length > 0) {
                        const medications = data.medications;
                        const mostUsed = medications[0];
                        const totalDoses = medications.reduce((sum, med) => sum + parseInt(med.total_quantity), 0);

                        document.getElementById('most-used-med').textContent = mostUsed.medicine_name;
                        document.getElementById('total-doses').textContent = totalDoses;

                        // For monthly usage, sum all quantities (assuming all are current month for simplicity)
                        document.getElementById('monthly-usage').textContent = totalDoses;

                        // For low stock and weekly prescriptions, fetch separately
                        fetchLowStockCount();
                        fetchWeeklyPrescriptions();
                    } else {
                        document.getElementById('most-used-med').textContent = 'N/A';
                        document.getElementById('total-doses').textContent = '0';
                        document.getElementById('monthly-usage').textContent = '0';
                        document.getElementById('low-stock-count').textContent = '0';
                        document.getElementById('weekly-prescriptions').textContent = '0';
                    }
                })
                .catch(error => {
                    console.log('Error fetching medication usage data:', error);
                });
        }

        function fetchLowStockCount() {
            fetch('get_low_stock_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('low-stock-count').textContent = data.low_stock_count;
                    } else {
                        document.getElementById('low-stock-count').textContent = '0';
                    }
                })
                .catch(error => {
                    console.log('Error fetching low stock count:', error);
                });
        }

        function fetchWeeklyPrescriptions() {
            fetch('get_weekly_prescriptions_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('weekly-prescriptions').textContent = data.weekly_prescriptions;
                    } else {
                        document.getElementById('weekly-prescriptions').textContent = '0';
                    }
                })
                .catch(error => {
                    console.log('Error fetching weekly prescriptions count:', error);
                });
        }



        function createVisitReasonsBarChart(reasons) {
            // Replace the entire analytics grid with a single large chart
            const analyticsContainer = document.querySelector('.analytics .grid');
            if (analyticsContainer) {
                analyticsContainer.innerHTML = `
                    <div class="col-span-3 bg-gray-50 rounded-lg p-6">
                        <canvas id="visitReasonsChart" style="width: 100%; height: 300px;"></canvas>
                    </div>
                `;
            }

            const ctx = document.getElementById('visitReasonsChart');
            if (!ctx) return;

            // Generate colors for each bar
            const colors = generateColors(reasons.length);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: reasons.map(item => item.reason.length > 20 ? item.reason.substring(0, 20) + '...' : item.reason),
                    datasets: [{
                        label: 'Number of Visits',
                        data: reasons.map(item => item.visit_count),
                        backgroundColor: colors.background,
                        borderColor: colors.border,
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                        hoverBackgroundColor: colors.hover,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Clinic Visit Reasons (Last 30 Days)',
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: {
                                top: 10,
                                bottom: 30
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            callbacks: {
                                label: function(context) {
                                    return 'Visits: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                lineWidth: 1
                            },
                            title: {
                                display: true,
                                text: 'Number of Visits',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                },
                                maxRotation: 45,
                                minRotation: 45
                            },
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Visit Reasons',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart',
                        delay: function(context) {
                            return context.dataIndex * 100;
                        }
                    },
                    onHover: (event, activeElements) => {
                        event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                    }
                }
            });
        }

        function generateColors(count) {
            const baseColors = [
                { bg: 'rgba(59, 130, 246, 0.8)', border: 'rgba(59, 130, 246, 1)', hover: 'rgba(59, 130, 246, 0.9)' }, // Blue
                { bg: 'rgba(239, 68, 68, 0.8)', border: 'rgba(239, 68, 68, 1)', hover: 'rgba(239, 68, 68, 0.9)' }, // Red
                { bg: 'rgba(34, 197, 94, 0.8)', border: 'rgba(34, 197, 94, 1)', hover: 'rgba(34, 197, 94, 0.9)' }, // Green
                { bg: 'rgba(234, 179, 8, 0.8)', border: 'rgba(234, 179, 8, 1)', hover: 'rgba(234, 179, 8, 0.9)' }, // Yellow
                { bg: 'rgba(147, 51, 234, 0.8)', border: 'rgba(147, 51, 234, 1)', hover: 'rgba(147, 51, 234, 0.9)' }, // Purple
                { bg: 'rgba(249, 115, 22, 0.8)', border: 'rgba(249, 115, 22, 1)', hover: 'rgba(249, 115, 22, 0.9)' }, // Orange
                { bg: 'rgba(6, 182, 212, 0.8)', border: 'rgba(6, 182, 212, 1)', hover: 'rgba(6, 182, 212, 0.9)' }, // Cyan
                { bg: 'rgba(236, 72, 153, 0.8)', border: 'rgba(236, 72, 153, 1)', hover: 'rgba(236, 72, 153, 0.9)' }, // Pink
                { bg: 'rgba(107, 114, 128, 0.8)', border: 'rgba(107, 114, 128, 1)', hover: 'rgba(107, 114, 128, 0.9)' }, // Gray
                { bg: 'rgba(245, 101, 101, 0.8)', border: 'rgba(245, 101, 101, 1)', hover: 'rgba(245, 101, 101, 0.9)' } // Light Red
            ];

            const background = [];
            const border = [];
            const hover = [];

            for (let i = 0; i < count; i++) {
                const color = baseColors[i % baseColors.length];
                background.push(color.bg);
                border.push(color.border);
                hover.push(color.hover);
            }

            return { background, border, hover };
        }

        function showNoDataMessage() {
            const analyticsContainer = document.querySelector('.analytics .grid');
            if (analyticsContainer) {
                analyticsContainer.innerHTML = `
                    <div class="col-span-3 bg-gray-50 rounded-lg p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-lg font-medium">No Visit Data Available</p>
                        <p class="text-gray-400 text-sm mt-1">Check back later for clinic visit statistics</p>
                    </div>
                `;
            }
        }
    </script>
    </body>
</html>
