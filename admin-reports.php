 <?php include 'user-role.php'; ?>
<?php include 'verifyer.php'; ?>
<?php
// Include necessary data files
include 'visits_data.php';
include 'medicine_data.php';
include 'functions.php';
include 'connection.php';

// Get data for reports
$allVisits = getAllClinicVisits([], 1000); // Get more visits for summary
$medicines = getAllMedicines();
$logsTable = generateLogsTable($pdo);

// Filter student visits
$studentVisits = array_filter($allVisits, function($visit) {
    return $visit['patient_type'] === 'Student';
});

// Filter staff visits
$staffVisits = array_filter($allVisits, function($visit) {
    return $visit['patient_type'] === 'Staff';
});

// Calculate student visit statistics and monthly aggregation
$totalStudentVisits = count($studentVisits);
$programStats = [];
$reasonStats = [];
$monthlyStudentVisits = [];

foreach ($studentVisits as $visit) {
    $program = $visit['program_name'] ?? 'Unknown';
    $reason = $visit['reason'] ?? 'Not specified';

    if (!isset($programStats[$program])) {
        $programStats[$program] = 0;
    }
    $programStats[$program]++;

    if (!isset($reasonStats[$reason])) {
        $reasonStats[$reason] = 0;
    }
    $reasonStats[$reason]++;

    // Aggregate visits by month (format: YYYY-MM)
    $month = date('Y-m', strtotime($visit['visit_date']));
    if (!isset($monthlyStudentVisits[$month])) {
        $monthlyStudentVisits[$month] = 0;
    }
    $monthlyStudentVisits[$month]++;
}

// Sort monthly visits by date ascending
ksort($monthlyStudentVisits);

$monthlyStaffVisits = [];
foreach ($staffVisits as $visit) {
    $month = date('Y-m', strtotime($visit['visit_date']));
    if (!isset($monthlyStaffVisits[$month])) {
        $monthlyStaffVisits[$month] = 0;
    }
    $monthlyStaffVisits[$month]++;
}
ksort($monthlyStaffVisits);

// Calculate medicine statistics
$totalMedicines = count($medicines);
$lowStockCount = 0;
$expiringSoon = 0;
$totalDispensed = 0;

// Get total dispensed medicines
try {
    $stmt = $pdo->query("SELECT SUM(quantity_dispensed) as total_dispensed FROM Medicine_Dispensing");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalDispensed = $result['total_dispensed'] ?? 0;
} catch (PDOException $e) {
    $totalDispensed = 0;
}

foreach ($medicines as $medicine) {
    if (($medicine['quantity'] ?? 0) < 10) {
        $lowStockCount++;
    }

    if (!empty($medicine['expiration_date'])) {
        $expiryDate = strtotime($medicine['expiration_date']);
        $now = time();
        $daysUntilExpiry = ($expiryDate - $now) / (60 * 60 * 24);

        if ($daysUntilExpiry <= 30 && $daysUntilExpiry > 0) {
            $expiringSoon++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin| Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="admin.css">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <style>
            @media print {
                /* Hide sidebar and mobile navigation */
                .sidebar, .lg\\:hidden {
                    display: none !important;
                }

                /* Adjust main content layout */
                .main-context {
                    margin-left: 0 !important;
                    width: 100% !important;
                }

                /* Adjust header layout */
                .flex.justify-between.items-center {
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }

                /* Ensure each report section starts on a new page */
                .space-y-6 > * {
                    page-break-before: always;
                    page-break-inside: avoid;
                    margin-bottom: 2rem !important;
                }

                /* Improve table printing */
                table {
                    font-size: 12px !important;
                    width: 100% !important;
                }

                th, td {
                    padding: 8px 4px !important;
                    border: 1px solid #e5e7eb !important;
                }

                /* Ensure colors are visible in print */
                .bg-blue-100 { background-color: #dbeafe !important; }
                .bg-green-100 { background-color: #dcfce7 !important; }
                .bg-yellow-100 { background-color: #fef3c7 !important; }
                .bg-red-100 { background-color: #fee2e2 !important; }
                .bg-purple-100 { background-color: #faf5ff !important; }
                .bg-indigo-100 { background-color: #eef2ff !important; }
                .bg-orange-100 { background-color: #fff7ed !important; }

                /* Text colors */
                .text-blue-600 { color: #2563eb !important; }
                .text-blue-800 { color: #1e40af !important; }
                .text-green-600 { color: #16a34a !important; }
                .text-green-800 { color: #166534 !important; }
                .text-yellow-600 { color: #ca8a04 !important; }
                .text-yellow-800 { color: #92400e !important; }
                .text-red-600 { color: #dc2626 !important; }
                .text-red-800 { color: #991b1b !important; }
                .text-purple-600 { color: #9333ea !important; }
                .text-purple-800 { color: #6b21a8 !important; }
                .text-indigo-600 { color: #4f46e5 !important; }
                .text-indigo-800 { color: #312e81 !important; }
                .text-orange-600 { color: #ea580c !important; }
                .text-orange-800 { color: #9a3412 !important; }

                /* Add page header */
                @page {
                    margin: 1in;
                    size: A4;
                }

                /* Add print header */
                body:before {
                    content: "SRCB Clinic Management System - Reports";
                    display: block;
                    font-size: 18px;
                    font-weight: bold;
                    text-align: center;
                    margin-bottom: 20px;
                    color: #1f2937;
                }

                /* Add print footer with date */
                body:after {
                    content: "Generated on: " attr(data-print-date);
                    display: block;
                    text-align: center;
                    margin-top: 20px;
                    font-size: 12px;
                    color: #6b7280;
                }
            }

            /* Hide print button when printing */
            @media print {
                .print\\:hidden {
                    display: none !important;
                }
            }
        </style>

        <script>
            // Add current date for print footer
            document.addEventListener('DOMContentLoaded', function() {
                const now = new Date();
                const dateString = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
                document.body.setAttribute('data-print-date', dateString);
            });

            // Modal functionality
            function openMonthModal(month, monthName) {
                const modal = document.getElementById('monthModal');
                const modalTitle = document.getElementById('modalTitle');
                const modalContent = document.getElementById('modalContent');

                modalTitle.textContent = 'Student Visits for ' + monthName;

                // Get visits for this month
                fetch('get_month_visits.php?month=' + month + '&patient_type=Student')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let html = '<div class="overflow-x-auto">';
                            html += '<table class="min-w-full divide-y divide-gray-200">';
                            html += '<thead class="bg-gray-50">';
                            html += '<tr>';
                            html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>';
                            html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit Date</th>';
                            html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>';
                            html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Treatment</th>';
                            html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>';
                            html += '</tr>';
                            html += '</thead>';
                            html += '<tbody class="bg-white divide-y divide-gray-200">';

                            if (data.visits.length > 0) {
                                data.visits.forEach(visit => {
                                    html += '<tr>';
                                    html += `<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${visit.patient_name || 'N/A'}</td>`;
                                    html += `<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${new Date(visit.visit_date).toLocaleDateString()}</td>`;
                                    html += `<td class="px-4 py-3 text-sm text-gray-500">${visit.reason || 'N/A'}</td>`;
                                    html += `<td class="px-4 py-3 text-sm text-gray-500">${visit.treatment || 'N/A'}</td>`;
                                    html += `<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${visit.program_name || 'N/A'}</td>`;
                                    html += '</tr>';
                                });
                            } else {
                                html += '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No visits found for this month</td></tr>';
                            }

                            html += '</tbody></table></div>';
                            modalContent.innerHTML = html;
                        } else {
                            modalContent.innerHTML = '<p class="text-red-500">Error loading visit data</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        modalContent.innerHTML = '<p class="text-red-500">Error loading visit data</p>';
                    });

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeMonthModal() {
                const modal = document.getElementById('monthModal');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Close modal when clicking outside
            document.addEventListener('click', function(event) {
                const modal = document.getElementById('monthModal');
                const modalContent = document.getElementById('modalContentWrapper');
                if (event.target === modal) {
                    closeMonthModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeMonthModal();
                }
            });

            // Print specific section function
            function printSection(sectionId) {
                const section = document.getElementById(sectionId);
                if (!section) {
                    alert('Section not found');
                    return;
                }

                // Create a new window for printing
                const printWindow = window.open('', '_blank');
                const styles = `
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f5f5f5; font-weight: bold; }
                        .text-center { text-align: center; }
                        .text-right { text-align: right; }
                        .font-bold { font-weight: bold; }
                        .mb-4 { margin-bottom: 16px; }
                        .mb-6 { margin-bottom: 24px; }
                        .text-blue-600 { color: #2563eb; }
                        .text-green-600 { color: #16a34a; }
                        .text-yellow-600 { color: #ca8a04; }
                        .text-red-600 { color: #dc2626; }
                        .text-purple-600 { color: #9333ea; }
                        .bg-blue-100 { background-color: #dbeafe; }
                        .bg-green-100 { background-color: #dcfce7; }
                        .bg-yellow-100 { background-color: #fef3c7; }
                        .bg-red-100 { background-color: #fee2e2; }
                        .bg-purple-100 { background-color: #faf5ff; }
                        .text-blue-800 { color: #1e40af; }
                        .text-green-800 { color: #166534; }
                        .text-yellow-800 { color: #92400e; }
                        .text-red-800 { color: #991b1b; }
                        .text-purple-800 { color: #6b21a8; }
                        .inline-flex { display: inline-flex; }
                        .items-center { align-items: center; }
                        .px-2.5 { padding-left: 10px; padding-right: 10px; }
                        .py-0.5 { padding-top: 2px; padding-bottom: 2px; }
                        .rounded-full { border-radius: 9999px; }
                        .text-xs { font-size: 12px; }
                        .font-medium { font-weight: 500; }
                        @media print {
                            body { margin: 0; }
                        }
                    </style>
                `;

                const sectionTitle = section.querySelector('h3').textContent;
                const currentDate = new Date().toLocaleDateString();
                const currentTime = new Date().toLocaleTimeString();

                printWindow.document.write('<html><head><title>SRCB Clinic - ' + sectionTitle + '</title>' + styles + '</head><body><h1 style="text-align: center; margin-bottom: 20px;">SRCB Clinic Management System</h1><h2 style="text-align: center; margin-bottom: 30px;">' + sectionTitle + '</h2>' + section.innerHTML + '<div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">Generated on: ' + currentDate + ' ' + currentTime + '</div></body></html>');

                printWindow.document.close();
                printWindow.focus();

                // Wait for content to load then print
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            }
        </script>
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
                <div class="flex justify-between items-center mb-6 pb-2 pt-[38px] pl-[60px] pr-[60px]">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Reports</h2>
                    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200 print:hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Print Reports</span>
                    </button>
                </div>

                    <div class="px-6 pb-6 space-y-6">

                    <!-- Student Visit Summary -->
                    <div class="bg-white rounded-lg shadow-md p-6 report-section" id="student-summary">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Student Visit Summary</h3>
                            <div class="flex items-center space-x-2">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <?php echo $totalStudentVisits; ?> Total Visits
                                </span>
                                <button onclick="printSection('student-summary')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm flex items-center space-x-1 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    <span>Print</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Visit Statistics -->
                            <div>
                                <h4 class="text-lg font-semibold text-gray-700 mb-3">Visit Statistics</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Student Visits:</span>
                                        <span class="font-semibold"><?php echo $totalStudentVisits; ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Unique Students:</span>
                                        <span class="font-semibold"><?php echo count(array_unique(array_column($studentVisits, 'patient_id'))); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Program Distribution -->
                            <div>
                                <h4 class="text-lg font-semibold text-gray-700 mb-3">Visits by Program</h4>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    <?php
                                    // Debug: Check what department levels exist in the data
                                    $departmentLevels = array_unique(array_column($studentVisits, 'department_level'));
                                    $departmentLevels = array_filter($departmentLevels); // Remove null/empty values

                                    if (empty($departmentLevels)) {
                                        // Fallback to original program stats if no department data
                                        arsort($programStats);
                                        foreach (array_slice($programStats, 0, 5) as $program => $count) {
                                            $percentage = $totalStudentVisits > 0 ? round(($count / $totalStudentVisits) * 100, 1) : 0;
                                            echo "<div class='flex justify-between'>
                                                    <span class='text-gray-600'>{$program}:</span>
                                                    <span class='font-semibold'>{$count} ({$percentage}%)</span>
                                                  </div>";
                                        }
                                    } else {
                                        // Group program stats by department level for display
                                        $groupedProgramStats = [];
                                        foreach ($studentVisits as $visit) {
                                            $department = $visit['department_level'] ?? 'Others';
                                            $program = $visit['program_name'] ?? 'Unknown';
                                            if (!isset($groupedProgramStats[$department])) {
                                                $groupedProgramStats[$department] = [];
                                            }
                                            if (!isset($groupedProgramStats[$department][$program])) {
                                                $groupedProgramStats[$department][$program] = 0;
                                            }
                                            $groupedProgramStats[$department][$program]++;
                                        }
                                        // Display grouped stats by department
                                        foreach ($groupedProgramStats as $department => $programs) {
                                            echo "<div class='mb-2 font-semibold text-gray-700'>" . htmlspecialchars(getDepartmentDisplayName($department)) . "</div>";
                                            arsort($programs);
                                            foreach ($programs as $program => $count) {
                                                $percentage = $totalStudentVisits > 0 ? round(($count / $totalStudentVisits) * 100, 1) : 0;
                                                echo "<div class='flex justify-between pl-4'>
                                                        <span class='text-gray-600'>{$program}:</span>
                                                        <span class='font-semibold'>{$count} ({$percentage}%)</span>
                                                      </div>";
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Common Reasons -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-lg font-semibold text-gray-700">Common Visit Reasons</h4>
                                <form method="GET" class="flex items-center space-x-2">
                                    <label for="reason_month" class="text-sm text-gray-600">Filter by Month:</label>
                                    <select name="reason_month" id="reason_month" onchange="this.form.submit()" class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">All Months</option>
                                        <?php
                                        $currentMonth = date('Y-m');
                                        foreach ($monthlyStudentVisits as $month => $count) {
                                            $formattedMonth = date('F Y', strtotime($month . '-01'));
                                            $selected = '';
                                            if (isset($_GET['reason_month']) && $_GET['reason_month'] == $month) {
                                                $selected = 'selected';
                                            } elseif (!isset($_GET['reason_month']) && $month == $currentMonth) {
                                                $selected = 'selected';
                                            }
                                            echo "<option value='{$month}' {$selected}>{$formattedMonth}</option>";
                                        }
                                        ?>
                                    </select>
                                </form>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php
                                // Get current month for default filtering
                                $currentMonth = date('Y-m');

                                // Filter visits by selected month or default to current month
                                $filteredVisits = $studentVisits;
                                $filteredTotal = $totalStudentVisits;
                                $selectedMonth = isset($_GET['reason_month']) ? $_GET['reason_month'] : $currentMonth;

                                if (!empty($selectedMonth)) {
                                    $filteredVisits = array_filter($studentVisits, function($visit) use ($selectedMonth) {
                                        $visitMonth = date('Y-m', strtotime($visit['visit_date']));
                                        return $visitMonth == $selectedMonth;
                                    });
                                    $filteredTotal = count($filteredVisits);
                                }

                                // Recalculate reason stats for filtered visits
                                $filteredReasonStats = [];
                                foreach ($filteredVisits as $visit) {
                                    $reason = $visit['reason'] ?? 'Not specified';
                                    if (!isset($filteredReasonStats[$reason])) {
                                        $filteredReasonStats[$reason] = 0;
                                    }
                                    $filteredReasonStats[$reason]++;
                                }

                                arsort($filteredReasonStats);
                                $topReasons = array_slice($filteredReasonStats, 0, 6);
                                foreach ($topReasons as $reason => $count) {
                                    $percentage = $filteredTotal > 0 ? round(($count / $filteredTotal) * 100, 1) : 0;
                                    echo "<div class='bg-gray-50 p-3 rounded-lg'>
                                            <div class='text-sm font-medium text-gray-800'>{$reason}</div>
                                            <div class='text-lg font-bold text-blue-600'>{$count}</div>
                                            <div class='text-xs text-gray-500'>{$percentage}% of visits</div>
                                          </div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Medicine Usage Report -->
                    <div class="bg-white rounded-lg shadow-md p-6 report-section" id="medicine-usage-report">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Medicine Usage Report</h3>
                            <div class="flex items-center space-x-2">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <?php echo $totalMedicines; ?> Medicines
                                </span>
                                <button onclick="printSection('medicine-usage-report')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm flex items-center space-x-1 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    <span>Print</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600"><?php echo $totalMedicines; ?></div>
                                <div class="text-sm text-gray-600">Total Medicines</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600"><?php echo $lowStockCount; ?></div>
                                <div class="text-sm text-gray-600">Low Stock Items</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600"><?php echo $expiringSoon; ?></div>
                                <div class="text-sm text-gray-600">Expiring Soon</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600"><?php echo $totalDispensed; ?></div>
                                <div class="text-sm text-gray-600">Total Dispensed</div>
                            </div>
                        </div>

                        <!-- Medicine Inventory Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicine Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiration</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                    usort($medicines, function($a, $b) {
                                        return ($a['quantity'] ?? 0) <=> ($b['quantity'] ?? 0);
                                    });

                                    foreach (array_slice($medicines, 0, 10) as $medicine) {
                                        $quantity = $medicine['quantity'] ?? 0;
                                        $statusClass = $quantity < 10 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                                        $statusText = $quantity < 10 ? 'Low Stock' : 'In Stock';

                                        $expiryDate = $medicine['expiration_date'] ?? '';
                                        $expiryStatus = '';
                                        if (!empty($expiryDate)) {
                                            $daysUntilExpiry = (strtotime($expiryDate) - time()) / (60 * 60 * 24);
                                            if ($daysUntilExpiry <= 30 && $daysUntilExpiry > 0) {
                                                $expiryStatus = 'text-orange-600';
                                            } elseif ($daysUntilExpiry <= 0) {
                                                $expiryStatus = 'text-red-600';
                                            }
                                        }

                                        echo "<tr>
                                                <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>{$medicine['medicine_name']}</td>
                                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$quantity}</td>
                                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$medicine['unit']}</td>
                                                <td class='px-6 py-4 whitespace-nowrap text-sm {$expiryStatus}'>{$expiryDate}</td>
                                                <td class='px-6 py-4 whitespace-nowrap'>
                                                    <span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$statusClass}'>{$statusText}</span>
                                                </td>
                                              </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Inventory Status Summary -->
                    <div class="bg-white rounded-lg shadow-md p-6 report-section" id="inventory-status-summary">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Inventory Status Summary</h3>
                            <div class="flex items-center space-x-2">
                                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                    Updated Live
                                </span>
                                <button onclick="printSection('inventory-status-summary')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm flex items-center space-x-1 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    <span>Print</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-blue-600"><?php echo $totalMedicines; ?></div>
                                <div class="text-sm text-blue-800">Total Items</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    <?php echo count(array_filter($medicines, function($m) { return ($m['quantity'] ?? 0) >= 50; })); ?>
                                </div>
                                <div class="text-sm text-green-800">Well Stocked</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-yellow-600"><?php echo $lowStockCount; ?></div>
                                <div class="text-sm text-yellow-800">Low Stock</div>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-red-600">
                                    <?php echo count(array_filter($medicines, function($m) { return ($m['quantity'] ?? 0) == 0; })); ?>
                                </div>
                                <div class="text-sm text-red-800">Out of Stock</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-purple-600"><?php echo $totalDispensed; ?></div>
                                <div class="text-sm text-purple-800">Total Dispensed</div>
                            </div>
                        </div>

                        <!-- Low Stock Alert -->
                        <?php if ($lowStockCount > 0): ?>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Low Stock Alert:</strong> <?php echo $lowStockCount; ?> medicine(s) are running low on stock (less than 10 units).
                                        Please consider restocking these items.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Expiring Soon Alert -->
                        <?php if ($expiringSoon > 0): ?>
                        <div class="bg-orange-50 border-l-4 border-orange-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-orange-700">
                                        <strong>Expiration Alert:</strong> <?php echo $expiringSoon; ?> medicine(s) will expire within 30 days.
                                        Please check and use these items first.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Staff Activity Report -->
                    <div class="bg-white rounded-lg shadow-md p-6 report-section" id="staff-activity-report">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Staff Activity Report</h3>
                            <div class="flex items-center space-x-2">
                                <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
                                    Recent Activities
                                </span>
                                <button onclick="printSection('staff-activity-report')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm flex items-center space-x-1 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    <span>Print</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600"><?php echo count($staffVisits); ?></div>
                                <div class="text-sm text-gray-600">Staff Clinic Visits</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    <?php
                                    // Count unique staff members who visited
                                    echo count(array_unique(array_column($staffVisits, 'patient_id')));
                                    ?>
                                </div>
                                <div class="text-sm text-gray-600">Active Staff Patients</div>
                            </div>
                        </div>

                        <!-- Staff Visit Details -->
                        <?php if (!empty($staffVisits)): ?>
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">Recent Staff Visits</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Treatment</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php
                                        $recentStaffVisits = array_slice($staffVisits, 0, 5);
                                        foreach ($recentStaffVisits as $visit) {
                                            echo "<tr>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>" . htmlspecialchars(getPatientName($visit)) . "</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . date('M j, Y', strtotime($visit['visit_date'])) . "</td>
                                                    <td class='px-6 py-4 text-sm text-gray-500'>" . htmlspecialchars(substr($visit['reason'] ?? 'N/A', 0, 30)) . "</td>
                                                    <td class='px-6 py-4 text-sm text-gray-500'>" . htmlspecialchars(substr($visit['treatment'] ?? 'N/A', 0, 30)) . "</td>
                                                  </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- System Activity Logs -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">System Activity Logs</h4>
                            <div class="bg-gray-50 rounded-lg p-4 max-h-96 overflow-y-auto">
                                <?php echo $logsTable; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    <script src="mobile-nav.js"></script>

    <!-- Month Details Modal -->
    <div id="monthModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white" id="modalContentWrapper">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Month Details</h3>
                    <button onclick="closeMonthModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modalContent" class="text-sm text-gray-500">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    </body>
</html>
