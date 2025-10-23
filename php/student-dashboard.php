<?php
session_start();

// Debug: Check session
error_log("Session check - user_id: " . ($_SESSION['user_id'] ?? 'not set') . ", user_type: " . ($_SESSION['user_type'] ?? 'not set'));

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    error_log("Redirecting to student-login.php - user_id: " . (isset($_SESSION['user_id']) ? 'set' : 'not set') . ", user_type: " . ($_SESSION['user_type'] ?? 'not set'));
    header('Location: student-login.php');
    exit();
}

$student_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Include required files
include 'connection.php';
include 'student-config.php';
include 'student-login-handler.php';
include 'student-dashboard-data.php';
include 'student-dashboard-js-data.php';

?>

<!DOCTYPE html>
<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo STUDENT_DASHBOARD_TITLE; ?></title>
    <link rel="stylesheet" href="../css/student.css">
    <link rel="stylesheet" href="../css/student-dashboard.css">
    <link rel="stylesheet" href="../css/student-alerts.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Header Section -->
    <header class="bg-gray-50 py-2.5 border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="SRCB-logo">
                        <img src="../images/SRCB.png" alt="SRCB logo" class="w-12 h-12">
                    </div>
                    <div class="clinic-logo">
                        <img src="../images/clinic.png" alt="clinic logo" class="w-12 h-12">
                    </div>
                </div>
                <div class="text-xl font-bold text-gray-700">SRCB Clinic</div>
            </div>
            <!-- Desktop Navigation -->
            <nav class="desktop-nav hidden md:block">
                <ul class="flex items-center gap-5 m-0 p-0 list-none">
                    <li>
                        <a href="#home" class="no-underline text-gray-700 font-bold px-3 py-2 rounded transition-colors duration-300 hover:bg-gray-200 hover:text-blue-600">Home</a>
                    </li>
                    <li>
                        <a href="#goals" class="no-underline text-gray-700 font-bold px-3 py-2 rounded transition-colors duration-300 hover:bg-gray-200 hover:text-blue-600">Our Goals</a>
                    </li>
                    <li>
                        <a href="#about" class="no-underline text-gray-700 font-bold px-3 py-2 rounded transition-colors duration-300 hover:bg-gray-200 hover:text-blue-600">About</a>
                    </li>
                    <li class="ml-2.5 pl-2.5 border-l border-gray-300 relative">
                        <div class="user-dropdown">
                            <button onclick="toggleDropdown()" class="no-underline text-gray-700 font-bold px-3 py-2 rounded transition-colors duration-300 hover:bg-gray-200 hover:text-blue-600 flex items-center gap-2">
                                <?php echo htmlspecialchars($full_name); ?>
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                <div class="p-4 border-b border-gray-100">
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($full_name); ?></p>
                                    <p class="text-sm text-gray-600">ID: <?php echo htmlspecialchars($student_id); ?></p>
                                </div>
                                <div class="p-2">
                                    <a href="logout.php" class="block w-full text-left px-3 py-2 text-red-600 hover:bg-red-50 rounded transition-colors duration-200">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" class="inline mr-2">
                                            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                                        </svg>
                                        Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </nav>

            <!-- Mobile Navigation -->
            <div class="mobile-nav md:hidden">
                <button onclick="toggleMobileMenu()" class="p-2 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
                
                <!-- Mobile Menu Dropdown -->
                <div id="mobileMenu" class="hidden absolute top-full right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="p-4 border-b border-gray-100">
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($full_name); ?></p>
                        <p class="text-sm text-gray-600">ID: <?php echo htmlspecialchars($student_id); ?></p>
                    </div>
                    <div class="p-2">
                        <a href="#home" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded transition-colors">Home</a>
                        <a href="#goals" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded transition-colors">Our Goals</a>
                        <a href="#about" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded transition-colors">About</a>
                        <hr class="my-2">
                        <a href="logout.php" class="block px-3 py-2 text-red-600 hover:bg-red-50 rounded transition-colors">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" class="inline mr-2">
                                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="min-h-screen">
        <!-- Home Section -->
        <div id="home" class="content-section py-12 px-5">
            <div class="max-w-6xl mx-auto text-center">
                <div class="hero-section mb-8">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4 drop-shadow-lg">
                        Welcome to SRCB Clinic
                    </h1>
                    <p class="text-lg sm:text-xl text-white mb-6 drop-shadow-md max-w-3xl mx-auto leading-relaxed px-4">
                        Your health quest begins here
                    </p>
                </div>

                <div class="feature-cards grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <!-- New Health Questionnaire Card -->
                    <div class="feature-card bg-white rounded-2xl p-8 shadow-xl text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                        <div class="card-icon w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-5">
                            <svg width="30" height="30" fill="#007bff" viewBox="0 0 24 24">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-700 mb-2.5">New Health Questionnaire</h3>
                        
                        <p class="text-gray-600 mb-6 leading-relaxed">Complete your health assessment and medical information</p>
                        
                        <button onclick="showMedicalHistory()" class="bg-blue-600 text-white border-0 py-3 px-8 rounded-lg font-semibold cursor-pointer transition-colors duration-300 w-full hover:bg-blue-700">
                            Start Assessment
                        </button>
                    </div>

                    <!-- View/Edit Health Questionnaire Card -->
                    <div class="feature-card bg-white rounded-2xl p-8 shadow-xl text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                        <div class="card-icon w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-5">
                            <svg width="30" height="30" fill="#28a745" viewBox="0 0 24 24">
                                <path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"/>
                            </svg>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-700 mb-2.5">My Health Records</h3>
                        
                        <p class="text-gray-600 mb-6 leading-relaxed">View or edit your existing health questionnaire</p>
                        
                        <button onclick="viewHealthQuestionnaire()" class="bg-green-600 text-white border-0 py-3 px-8 rounded-lg font-semibold cursor-pointer transition-colors duration-300 w-full hover:bg-green-700">
                            View/Edit Records
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Goals Section -->
        <div id="goals" class="content-section hidden py-20 px-5">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-center text-blue-600 mb-10 text-4xl font-bold">Our Goals</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white p-8 rounded-2xl shadow-lg">
                        <h3 class="text-gray-700 mb-4 text-xl font-semibold">Quality Healthcare</h3>
                        <p class="text-gray-600 leading-relaxed">To provide quality healthcare services to students with compassionate care and modern medical practices.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-lg">
                        <h3 class="text-gray-700 mb-4 text-xl font-semibold">Health & Wellness</h3>
                        <p class="text-gray-600 leading-relaxed">To promote health and wellness in the community through education and preventive care programs.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- About Section -->
        <div id="about" class="content-section hidden py-20 px-5">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-center text-blue-600 mb-10 text-4xl font-bold">About</h2>
                <div class="bg-white p-10 rounded-2xl shadow-lg text-center">
                    <p class="text-gray-600 leading-relaxed text-lg">The Integrated Digital Clinic Management System of St. Rita's College of Balingasag is designed to streamline clinic operations and provide efficient healthcare services to our students and staff.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Health Questionnaire Form -->
    <?php include 'health-questionnaire-form-classic.php'; ?>
    
    <!-- Include Health Records Viewer -->
    <?php include 'health-records-viewer.php'; ?>

    <!-- Footer -->
    <footer class="footer bg-gray-100 py-12 px-5">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Logo and Info -->
                <div class="footer-brand">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center gap-2">
                            <img src="../images/SRCB.png" alt="SRCB logo" class="w-12 h-12">
                            <img src="../images/clinic.png" alt="clinic logo" class="w-12 h-12">
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">SRCB Clinic</h3>
                    <p class="text-gray-600 mb-4">Powered by BSIT Students</p>
                    <div class="flex gap-2">
                        <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                    </div>
                    <button class="back-to-top bg-blue-500 text-white px-4 py-2 rounded mt-4 hover:bg-blue-600 transition-colors" onclick="scrollToTop()">
                        Back to Top
                    </button>
                </div>

                <!-- Site Map -->
                <div class="footer-links">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Site Map</h4>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-600 hover:text-blue-600 transition-colors">Homepage</a></li>
                        <li><a href="javascript:void(0)" onclick="showMedicalHistory()" class="text-gray-600 hover:text-blue-600 transition-colors">New Health Assessment</a></li>
                        <li><a href="javascript:void(0)" onclick="viewHealthQuestionnaire()" class="text-gray-600 hover:text-blue-600 transition-colors">My Health Records</a></li>
                        <li><a href="#goals" class="text-gray-600 hover:text-blue-600 transition-colors">Vision & Mission</a></li>
                        <li><a href="#about" class="text-gray-600 hover:text-blue-600 transition-colors">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div class="footer-legal">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-blue-600 transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-blue-600 transition-colors">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Copyright Bar -->
    <div class="copyright bg-blue-500 text-white text-center py-3">
        <p class="text-sm">Copyright © 2025, SRCB Clinic, All Rights Reserved.</p>
    </div>

    <!-- Pass PHP data to JavaScript -->
    <script>
        <?php echo generateStudentDataJS($student_id, $full_name, $student, $all_programs, $all_sections); ?>
        <?php echo generateLoginSuccessJS($full_name, $student_id); ?>
    </script>
    
    <!-- External JavaScript -->
    <script src="../js/student-login-success.js"></script>
    <script src="../js/utils.js"></script>
    <script src="../js/forms.js"></script>
    <script src="../js/student-dashboard.js"></script>
    
    <!-- Navigation JavaScript -->
    <script>
        // Toggle user dropdown
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Toggle mobile menu
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const mobileMenu = document.getElementById('mobileMenu');
            const dropdownButton = event.target.closest('[onclick="toggleDropdown()"]');
            const mobileButton = event.target.closest('[onclick="toggleMobileMenu()"]');
            
            // Close user dropdown
            if (!dropdownButton && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
            
            // Close mobile menu
            if (!mobileButton && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('nav a[href^="#"]');
            const sections = document.querySelectorAll('.content-section');

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    
                    // Hide all sections
                    sections.forEach(section => {
                        section.classList.add('hidden');
                    });
                    
                    // Show target section
                    const targetSection = document.getElementById(targetId);
                    if (targetSection) {
                        targetSection.classList.remove('hidden');
                    }
                    
                    // Update active nav link
                    navLinks.forEach(navLink => {
                        navLink.classList.remove('bg-blue-100', 'text-blue-600');
                    });
                    this.classList.add('bg-blue-100', 'text-blue-600');
                });
            });

            // Show home section by default
            document.getElementById('home').classList.remove('hidden');
        });

        // Back to top functionality
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // View/Edit Health Questionnaire functionality
        async function viewHealthQuestionnaire() {
            try {
                // Show loading indicator
                Swal.fire({
                    title: 'Loading...',
                    text: 'Checking your health records',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Check if student has existing health questionnaire data
                console.log('Fetching health questionnaire data...');
                
                // Use the fixed backend API to get health record data
                const response = await fetch('health-questionnaire-backend-api.php?action=get_record');
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('HTTP Error Response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }
                
                // Get response as text first to debug JSON issues
                const responseText = await response.text();
                console.log('Raw Response:', responseText);
                
                let result;
                try {
                    result = JSON.parse(responseText);
                    console.log('Parsed API Response:', result);
                } catch (jsonError) {
                    console.error('JSON Parse Error:', jsonError);
                    console.error('Response Text:', responseText);
                    throw new Error(`Invalid JSON response: ${jsonError.message}. Raw response: ${responseText.substring(0, 200)}...`);
                }

                if (result.success) {
                    if (result.exists && result.data) {
                        // Student has existing data - show view/edit options
                        Swal.fire({
                            title: 'My Health Records',
                            html: `
                                <div style="text-align: center; padding: 20px;">
                                    <div style="font-size: 48px; margin-bottom: 20px;">📋</div>
                                    <h3 style="margin: 0 0 15px 0; color: #333;">Health Questionnaire Found</h3>
                                    <p style="margin: 0 0 20px 0; color: #666;">
                                        You have completed your health questionnaire. What would you like to do?
                                    </p>
                                    <div style="display: flex; gap: 15px; justify-content: center; margin-top: 25px;">
                                        <button onclick="viewExistingRecord()" style="background: #28a745; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                            📄 View Records
                                        </button>
                                        <button onclick="editExistingRecord()" style="background: #007bff; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                            ✏️ Edit Records
                                        </button>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: false,
                            showCancelButton: true,
                            cancelButtonText: 'Close',
                            width: '500px'
                        });
                    } else {
                        // No existing data - prompt to create new
                        Swal.fire({
                            title: 'No Health Records Found',
                            html: `
                                <div style="text-align: center; padding: 20px;">
                                    <div style="font-size: 48px; margin-bottom: 20px;">📝</div>
                                    <h3 style="margin: 0 0 15px 0; color: #333;">Create Your Health Record</h3>
                                    <p style="margin: 0 0 20px 0; color: #666;">
                                        You haven't completed your health questionnaire yet. Would you like to start now?
                                    </p>
                                </div>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Start Questionnaire',
                            cancelButtonText: 'Maybe Later',
                            confirmButtonColor: '#007bff'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                showMedicalHistory();
                            }
                        });
                    }
                } else {
                    throw new Error(result.message || 'Failed to load health records');
                }
            } catch (error) {
                console.error('Error loading health records:', error);
                
                let errorMessage = 'Unable to load your health records. Please try again later.';
                let debugInfo = '';
                
                if (error.message) {
                    debugInfo = `\n\nError details: ${error.message}`;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: errorMessage + debugInfo,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    footer: 'Check the browser console for more details'
                });
            }
        }

        // View existing health record (comprehensive review format)
        async function viewExistingRecord() {
            Swal.close();
            
            try {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Retrieving your health records',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Get complete health record data
                const response = await fetch('health-questionnaire-backend-api.php?action=get_record');
                const result = await response.json();
                
                Swal.close();
                
                if (result.success && result.exists && result.data) {
                    // Show comprehensive health records viewer
                    showHealthRecordsViewer(result);
                } else {
                    Swal.fire({
                        title: 'No Records Found',
                        text: 'No health records found to display.',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                }
                
            } catch (error) {
                console.error('Error loading health records:', error);
                Swal.close();
                Swal.fire({
                    title: 'Error',
                    text: 'Unable to load health records. Please try again later.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Edit existing health record
        function editExistingRecord() {
            Swal.close();
            // Show the health questionnaire modal in edit mode
            const modal = document.getElementById('healthQuestionnaireModal');
            if (modal) {
                modal.classList.remove('hidden');
                
                // Initialize the health questionnaire for editing
                if (typeof initializeHealthQuestionnaire === 'function') {
                    initializeHealthQuestionnaire();
                } else {
                    console.error('Health questionnaire initialization function not found');
                    Swal.fire({
                        title: 'Error',
                        text: 'Health questionnaire system is not available. Please try again later.',
                        icon: 'error'
                    });
                }
            } else {
                console.error('Health questionnaire modal not found');
                Swal.fire({
                    title: 'Error',
                    text: 'Health questionnaire form is not available. Please refresh the page.',
                    icon: 'error'
                });
            }
        }

        // Make form read-only for viewing
        function makeFormReadOnly() {
            const modal = document.getElementById('healthQuestionnaireModal');
            if (modal) {
                // Disable all input fields
                const inputs = modal.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    if (!input.hasAttribute('readonly') && !input.hasAttribute('disabled')) {
                        input.setAttribute('readonly', 'readonly');
                        input.style.backgroundColor = '#f8f9fa';
                        input.style.cursor = 'default';
                    }
                });

                // Hide save and submit buttons, only show navigation
                const saveBtn = modal.querySelector('#saveBtn');
                const submitBtn = modal.querySelector('#submitBtn');
                if (saveBtn) saveBtn.style.display = 'none';
                if (submitBtn) submitBtn.style.display = 'none';

                // Update modal title to indicate view mode
                const modalTitle = modal.querySelector('h2');
                if (modalTitle) {
                    modalTitle.textContent = 'Health Questionnaire (View Mode)';
                }
            }
        }

    </script>
    
    <!-- Include SweetAlert2 for the original health questionnaire -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
