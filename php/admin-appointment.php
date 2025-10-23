<?php include 'connection.php'; ?>
<?php include 'user-role.php'; ?>
<?php include 'verifyer.php'; ?>
<?php include 'admin-user-data.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin| Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="../css/admin.css">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <!-- FullCalendar CSS -->
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
        <style>
                        .fc-daygrid-day:hover {
                @apply bg-gray-100 cursor-pointer;
            }
            .fc-day-today {
                @apply bg-blue-100 !important;
            }
            .appointment-slot {
                @apply bg-emerald-500 text-white px-1.5 py-0.5 rounded text-xs my-0.5;
            }
            .appointment-slot.occupied {
                @apply bg-red-500;
            }
            .appointment-slot.available {
                @apply bg-emerald-500;
            }
            .calendar-container {
                @apply max-w-full mx-auto;
            }
            .appointment-form {
                @apply bg-white p-5 rounded-lg shadow-md mb-5;
            }
            .time-slots {
                @apply grid grid-cols-[repeat(auto-fit,minmax(120px,1fr))] gap-2.5 mt-2.5;
            }
            .time-slot {
                @apply px-3 py-2 border border-gray-300 rounded text-center cursor-pointer transition-all duration-200;
            }
            .time-slot:hover {
                @apply bg-gray-100;
            }
            .time-slot.selected {
                @apply bg-blue-500 text-white border-blue-500;
            }
            .time-slot.occupied {
                @apply bg-red-100 text-red-600 cursor-not-allowed;
            }
        </style>
    </head>

    <body>
        <div class="context flex flex-col lg:flex-row h-screen">
            <div class="side-bar hidden lg:flex lg:w-[300px] h-screen bg-white flex-col items-center fixed lg:relative">
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
                <div class="flex justify-between items-center mb-6 pb-2 pt-[38px] pl-[60px] pr-[60px]">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Appointments Calendar</h2>
                    <button id="addAppointmentBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add Appointment</span>
                    </button>
                </div>

                <div class="px-6 pb-6">
                    <!-- Section Filter -->
                    <div class="mb-6 bg-white rounded-lg shadow-md p-6" style="display:none;">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Filter by Section</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="departmentSelect" class="block text-sm font-medium text-gray-700">Department</label>
                                <select id="departmentSelect" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" disabled>
                                    <option value="">Select Department</option>
                                    <option value="College">College</option>
                                    <option value="SHS">Senior High School</option>
                                    <option value="JHS">Junior High School</option>
                                    <option value="Grade School">Grade School</option>
                                </select>
                            </div>
                            <div>
                                <label for="programSelect" class="block text-sm font-medium text-gray-700">Program</label>
                                <select id="programSelect" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" disabled>
                                    <option value="">Select Program</option>
                                </select>
                            </div>
                            <div>
                                <label for="sectionSelect" class="block text-sm font-medium text-gray-700">Section</label>
                                <select id="sectionSelect" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" disabled>
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Container -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <div id="calendar" class="calendar-container"></div>
                    </div>

                    <!-- Appointments List -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Today's Appointments</h3>
                        <div id="todayAppointments" class="space-y-4">
                            <!-- Appointments will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Appointment Modal -->
        <div id="appointmentModal" class="fixed inset-0 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Schedule New Appointment</h3>
                        <button onclick="closeAppointmentModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="appointmentForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="appointmentDepartment" class="block text-sm font-medium text-gray-700">Department</label>
                                <select id="appointmentDepartment" name="appointmentDepartment" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Department</option>
                                    <option value="College">College</option>
                                    <option value="SHS">Senior High School</option>
                                    <option value="JHS">Junior High School</option>
                                    <option value="Grade School">Grade School</option>
                                </select>
                            </div>
                            <div>
                                <label for="appointmentProgram" class="block text-sm font-medium text-gray-700">Program</label>
                                <select id="appointmentProgram" name="appointmentProgram" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" disabled>
                                    <option value="">Select Program</option>
                                </select>
                            </div>
                            <div>
                                <label for="appointmentSection" class="block text-sm font-medium text-gray-700">Section</label>
                                <select id="appointmentSection" name="appointmentSection" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" disabled>
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="appointmentDate" class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" id="appointmentDate" name="appointmentDate" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="appointmentStartTime" class="block text-sm font-medium text-gray-700">Start Time</label>
                                <input type="time" id="appointmentStartTime" name="appointmentStartTime" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="appointmentEndTime" class="block text-sm font-medium text-gray-700">End Time</label>
                                <input type="time" id="appointmentEndTime" name="appointmentEndTime" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Visit</label>
                            <textarea id="reason" name="reason" rows="3" required
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                            <textarea id="notes" name="notes" rows="2"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeAppointmentModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Schedule Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Appointment Details Modal -->
        <div id="detailsModal" class="fixed inset-0 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Appointment Details</h3>
                        <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="appointmentDetails" class="space-y-4">
                        <!-- Details will be loaded here -->
                    </div>
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button onclick="editAppointment()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                        <button onclick="deleteAppointment()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script src="../mobile-nav.js"></script>
        <!-- FullCalendar JS -->
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let calendar;
            let selectedDate = null;
            let appointments = [];
            let departments = [];
            let programs = [];
            let sections = [];

            document.addEventListener('DOMContentLoaded', function() {
                initializeCalendar();
                loadAppointments();
                loadDepartments();
                setupEventListeners();
            });

            function loadDepartments() {
                // Departments are static in the select, no need to fetch
                departments = ['College', 'SHS', 'JHS', 'Grade School'];
            }

            function loadPrograms(department) {
                const programSelect = document.getElementById('programSelect');
                programSelect.innerHTML = '<option value="">Select Program</option>';
                document.getElementById('sectionSelect').innerHTML = '<option value="">Select Section</option>';
                document.getElementById('sectionSelect').disabled = true;

                if (!department) {
                    programSelect.disabled = true;
                    return;
                }

                fetch(`get_programs.php?department=${encodeURIComponent(department)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            programs = data.programs;
                            programs.forEach(program => {
                                const option = document.createElement('option');
                                option.value = program.program_id;
                                option.textContent = program.program_name;
                                programSelect.appendChild(option);
                            });
                            programSelect.disabled = false;
                        } else {
                            programSelect.disabled = true;
                        }
                    })
                    .catch(() => {
                        programSelect.disabled = true;
                    });
            }

            function loadSections(programId) {
                const sectionSelect = document.getElementById('sectionSelect');
                sectionSelect.innerHTML = '<option value="">Select Section</option>';

                if (!programId) {
                    sectionSelect.disabled = true;
                    return;
                }

                fetch(`get_sections.php?program_id=${encodeURIComponent(programId)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            sections = data.sections;
                            sections.forEach(section => {
                                const option = document.createElement('option');
                                option.value = section.section_id;
                                option.textContent = section.section_name;
                                sectionSelect.appendChild(option);
                            });
                            sectionSelect.disabled = false;
                        } else {
                            sectionSelect.disabled = true;
                        }
                    })
                    .catch(() => {
                        sectionSelect.disabled = true;
                    });
            }

            function loadAppointmentPrograms(department) {
                const programSelect = document.getElementById('appointmentProgram');
                programSelect.innerHTML = '<option value="">Select Program</option>';
                document.getElementById('appointmentSection').innerHTML = '<option value="">Select Section</option>';
                document.getElementById('appointmentSection').disabled = true;

                if (!department) {
                    programSelect.disabled = true;
                    return;
                }

                fetch(`get_programs.php?department=${encodeURIComponent(department)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            programs = data.programs;
                            programs.forEach(program => {
                                const option = document.createElement('option');
                                option.value = program.program_id;
                                option.textContent = program.program_name;
                                programSelect.appendChild(option);
                            });
                            programSelect.disabled = false;
                        } else {
                            programSelect.disabled = true;
                        }
                    })
                    .catch(() => {
                        programSelect.disabled = true;
                    });
            }

            function loadAppointmentSections(programId) {
                const sectionSelect = document.getElementById('appointmentSection');
                sectionSelect.innerHTML = '<option value="">Select Section</option>';

                if (!programId) {
                    sectionSelect.disabled = true;
                    return;
                }

                fetch(`get_sections.php?program_id=${encodeURIComponent(programId)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            sections = data.sections;
                            sections.forEach(section => {
                                const option = document.createElement('option');
                                option.value = section.section_id;
                                option.textContent = section.section_name;
                                sectionSelect.appendChild(option);
                            });
                            sectionSelect.disabled = false;
                        } else {
                            sectionSelect.disabled = true;
                        }
                    })
                    .catch(() => {
                        sectionSelect.disabled = true;
                    });
            }

            // Removed event listeners and filterAppointments function calls related to section filter
            // The filter UI is hidden and disabled, so no filtering is applied
            function initializeCalendar() {
                const calendarEl = document.getElementById('calendar');

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    height: 600,
                    events: [],
                    dateClick: function(info) {
                        selectedDate = info.dateStr;
                        openAppointmentModal();
                        document.getElementById('appointmentDate').value = selectedDate;
                    },
                    eventClick: function(info) {
                        showAppointmentDetails(info.event.id);
                    },
                    dayCellDidMount: function(info) {
                        // Mark Sundays red
                        if (info.date.getDay() === 0) { // 0 = Sunday
                            info.el.style.backgroundColor = '#fee2e2';
                            info.el.style.color = '#dc2626';
                        }

                        // Add custom styling for days with appointments
                        const dateStr = info.date.toISOString().split('T')[0];
                        const dayAppointments = appointments.filter(apt => apt.date === dateStr);

                        if (dayAppointments.length > 0) {
                            // If it's Sunday, use a different background for appointments
                            if (info.date.getDay() === 0) {
                                info.el.style.backgroundColor = '#fca5a5'; // Lighter red for Sunday with appointments
                            } else {
                                info.el.style.backgroundColor = '#fef3c7';
                            }
                            info.el.style.position = 'relative';

                            const indicator = document.createElement('div');
                            indicator.className = 'absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full';
                            info.el.appendChild(indicator);
                        }
                    }
                });

                calendar.render();
            }

            function loadAppointments() {
                // Fetch appointments from API for the current year
                const currentYear = new Date().getFullYear();
                const startDate = `${currentYear}-01-01`;
                const endDate = `${currentYear}-12-31`;

                fetch(`appointment-actions.php?action=get-by-date-range&start_date=${startDate}&end_date=${endDate}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('API Response:', data); // Debug log
                        if (data.success) {
                            appointments = data.appointments.map(apt => {
                                console.log('Appointment data:', apt); // Debug log
                                return {
                                    id: apt.appointment_id,
                                    date: apt.appointment_date,
                                    start_time: apt.start_time,
                                    end_time: apt.end_time,
                                    reason: apt.reason,
                                    notes: apt.notes,
                                    status: apt.status,
                                    section_id: apt.section_id,
                                    section_name: apt.section_name,
                                    program_id: apt.program_id,
                                    program_name: apt.program_name,
                                    department: apt.department
                                };
                            });
                            console.log('Mapped appointments:', appointments); // Debug log
                        } else {
                            appointments = [];
                            console.error('Failed to load appointments:', data.message);
                        }
                        updateCalendarEvents();
                        updateTodayAppointments();
                    })
                    .catch(error => {
                        console.error('Error loading appointments:', error);
                        appointments = [];
                        updateCalendarEvents();
                        updateTodayAppointments();
                    });
            }

            function getPhilippineHolidays(year) {
                const holidays = [
                    { date: `${year}-01-01`, title: "New Year's Day", type: "regular" },
                    { date: `${year}-04-09`, title: "Araw ng Kagitingan", type: "regular" },
                    { date: `${year}-05-01`, title: "Labor Day", type: "regular" },
                    { date: `${year}-06-12`, title: "Independence Day", type: "regular" },
                    { date: `${year}-11-30`, title: "Bonifacio Day", type: "regular" },
                    { date: `${year}-12-25`, title: "Christmas Day", type: "regular" },
                    { date: `${year}-12-30`, title: "Rizal Day", type: "regular" }
                ];

                // Calculate Holy Week dates (approximate - Maundy Thursday, Good Friday, Black Saturday)
                const easterDate = getEasterDate(year);
                const maundyThursday = new Date(easterDate);
                maundyThursday.setDate(easterDate.getDate() - 3);
                const goodFriday = new Date(easterDate);
                goodFriday.setDate(easterDate.getDate() - 2);
                const blackSaturday = new Date(easterDate);
                blackSaturday.setDate(easterDate.getDate() - 1);

                holidays.push(
                    { date: maundyThursday.toISOString().split('T')[0], title: "Maundy Thursday", type: "holy-week" },
                    { date: goodFriday.toISOString().split('T')[0], title: "Good Friday", type: "holy-week" },
                    { date: blackSaturday.toISOString().split('T')[0], title: "Black Saturday", type: "holy-week" }
                );

                // National Heroes Day (Last Monday of August)
                const lastMondayAugust = getLastMondayOfMonth(year, 7); // July is 7 (0-indexed)
                holidays.push({ date: lastMondayAugust, title: "National Heroes Day", type: "regular" });

                return holidays;
            }

            function getEasterDate(year) {
                // Meeus/Jones/Butcher algorithm for calculating Easter date
                const a = year % 19;
                const b = Math.floor(year / 100);
                const c = year % 100;
                const d = Math.floor(b / 4);
                const e = b % 4;
                const f = Math.floor((b + 8) / 25);
                const g = Math.floor((b - f + 1) / 3);
                const h = (19 * a + b - d - g + 15) % 30;
                const i = Math.floor(c / 4);
                const k = c % 4;
                const l = (32 + 2 * e + 2 * i - h - k) % 7;
                const m = Math.floor((a + 11 * h + 22 * l) / 451);
                const month = Math.floor((h + l - 7 * m + 114) / 31);
                const day = ((h + l - 7 * m + 114) % 31) + 1;

                return new Date(year, month - 1, day);
            }

            function getLastMondayOfMonth(year, month) {
                const lastDay = new Date(year, month + 1, 0);
                const lastDayOfWeek = lastDay.getDay();
                const daysToSubtract = lastDayOfWeek === 1 ? 0 : (lastDayOfWeek === 0 ? 6 : lastDayOfWeek - 1);
                const lastMonday = new Date(lastDay);
                lastMonday.setDate(lastDay.getDate() - daysToSubtract);
                return lastMonday.toISOString().split('T')[0];
            }

            function updateCalendarEvents(filteredAppointments = null) {
                const currentYear = new Date().getFullYear();
                const holidays = getPhilippineHolidays(currentYear);

                const appointmentEvents = (filteredAppointments || appointments).map(apt => ({
                    id: apt.id,
                    title: `${apt.section_name} (${apt.start_time} - ${apt.end_time})`,
                    start: `${apt.date}T${apt.start_time}`,
                    end: `${apt.date}T${apt.end_time}`,
                    backgroundColor: apt.status === 'completed' ? '#10b981' : '#3b82f6',
                    borderColor: apt.status === 'completed' ? '#059669' : '#2563eb',
                    className: 'appointment-event'
                }));

                const holidayEvents = holidays.map(holiday => ({
                    id: `holiday-${holiday.date}`,
                    title: holiday.title,
                    start: holiday.date,
                    allDay: true,
                    backgroundColor: holiday.type === 'holy-week' ? '#dc2626' : '#f59e0b',
                    borderColor: holiday.type === 'holy-week' ? '#b91c1c' : '#d97706',
                    className: 'holiday-event',
                    display: 'background'
                }));

                const allEvents = [...appointmentEvents, ...holidayEvents];

                calendar.removeAllEvents();
                calendar.addEventSource(allEvents);
            }

            function updateTodayAppointments(filteredAppointments = null) {
                const today = new Date().toISOString().split('T')[0];
                const todayApts = (filteredAppointments || appointments).filter(apt => apt.date === today);

                const container = document.getElementById('todayAppointments');

                if (todayApts.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 text-center py-4">No appointments scheduled for today</p>';
                    return;
                }

                container.innerHTML = todayApts.map(apt => `
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">${apt.section_name}</h4>
                            <p class="text-sm text-gray-600">${apt.department} • ${apt.start_time} - ${apt.end_time}</p>
                            <p class="text-sm text-gray-500">${apt.reason}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full ${
                                apt.status === 'completed' ? 'bg-green-100 text-green-800' :
                                apt.status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                'bg-blue-100 text-blue-800'
                            }">
                                ${apt.status}
                            </span>
                            <button onclick="showAppointmentDetails(${apt.id})" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `).join('');
            }

            function setupEventListeners() {
                document.getElementById('addAppointmentBtn').addEventListener('click', openAppointmentModal);
                document.getElementById('appointmentForm').addEventListener('submit', handleAppointmentSubmit);

                // Add event listeners for modal dropdowns
                document.getElementById('appointmentDepartment').addEventListener('change', function() {
                    loadAppointmentPrograms(this.value);
                });
                document.getElementById('appointmentProgram').addEventListener('change', function() {
                    loadAppointmentSections(this.value);
                });
            }

            function openAppointmentModal() {
                document.getElementById('appointmentModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Ensure the form uses the create handler (in case it was changed to update)
                const form = document.getElementById('appointmentForm');
                form.removeEventListener('submit', form.updateHandler || (() => {}));
                form.addEventListener('submit', handleAppointmentSubmit);
            }

            function closeAppointmentModal() {
                document.getElementById('appointmentModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
                document.getElementById('appointmentForm').reset();
            }

            function closeDetailsModal() {
                document.getElementById('detailsModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            function handleAppointmentSubmit(e) {
                e.preventDefault();

                const formData = new FormData(e.target);
                const appointmentData = {
                    section_id: formData.get('appointmentSection'),
                    program_id: formData.get('appointmentProgram'),
                    department: formData.get('appointmentDepartment'),
                    appointment_date: formData.get('appointmentDate'),
                    start_time: formData.get('appointmentStartTime'),
                    end_time: formData.get('appointmentEndTime'),
                    reason: formData.get('reason'),
                    notes: formData.get('notes'),
                    status: 'scheduled'
                };

                fetch('appointment-actions.php?action=create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(appointmentData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload appointments after successful creation
                        loadAppointments();
                        closeAppointmentModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Appointment scheduled successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to schedule appointment: ' + data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while scheduling the appointment.'
                    });
                });
            }

            function showAppointmentDetails(appointmentId) {
                const appointment = appointments.find(apt => apt.id == appointmentId);
                if (!appointment) return;

                const detailsContainer = document.getElementById('appointmentDetails');
                detailsContainer.innerHTML = `
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <p class="mt-1 text-sm text-gray-900">${appointment.department}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Program</label>
                            <p class="mt-1 text-sm text-gray-900">${appointment.program_name}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Section</label>
                            <p class="mt-1 text-sm text-gray-900">${appointment.section_name}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date & Time</label>
                            <p class="mt-1 text-sm text-gray-900">${appointment.date} from ${appointment.start_time} to ${appointment.end_time}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reason</label>
                            <p class="mt-1 text-sm text-gray-900">${appointment.reason}</p>
                        </div>
                        ${appointment.notes ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <p class="mt-1 text-sm text-gray-900">${appointment.notes}</p>
                        </div>
                        ` : ''}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1 text-sm text-gray-900">${appointment.status}</p>
                        </div>
                    </div>
                `;

                document.getElementById('detailsModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            // Close modals when clicking outside
            document.addEventListener('click', function(event) {
                const appointmentModal = document.getElementById('appointmentModal');
                const detailsModal = document.getElementById('detailsModal');

                if (event.target === appointmentModal) {
                    closeAppointmentModal();
                }
                if (event.target === detailsModal) {
                    closeDetailsModal();
                }
            });

            // Close modals with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeAppointmentModal();
                    closeDetailsModal();
                }
            });

            // Edit appointment function
            function editAppointment() {
                const detailsModal = document.getElementById('detailsModal');
                const appointmentId = appointments.find(apt => {
                    return document.getElementById('appointmentDetails').innerHTML.includes(apt.id);
                })?.id;

                if (!appointmentId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to find appointment to edit.'
                    });
                    return;
                }

                const appointment = appointments.find(apt => apt.id == appointmentId);
                if (!appointment) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Appointment not found.'
                    });
                    return;
                }

                // Populate the appointment form with existing data
                document.getElementById('appointmentDepartment').value = appointment.department;
                loadAppointmentPrograms(appointment.department);
                setTimeout(() => {
                    document.getElementById('appointmentProgram').value = appointment.program_id || '';
                    loadAppointmentSections(appointment.program_id);
                    setTimeout(() => {
                        document.getElementById('appointmentSection').value = appointment.section_id || '';
                    }, 300);
                }, 300);
                document.getElementById('appointmentDate').value = appointment.date;
                document.getElementById('appointmentStartTime').value = appointment.start_time;
                document.getElementById('appointmentEndTime').value = appointment.end_time;
                document.getElementById('reason').value = appointment.reason;
                document.getElementById('notes').value = appointment.notes || '';

                // Show the appointment modal and hide the details modal
                detailsModal.classList.add('hidden');
                openAppointmentModal();

                // Change the form submit handler to update instead of create
                const form = document.getElementById('appointmentForm');
                form.removeEventListener('submit', handleAppointmentSubmit);
                form.addEventListener('submit', function updateHandler(e) {
                    e.preventDefault();

                    const formData = new FormData(form);
                    const updatedData = {
                        appointment_id: appointment.id,
                        section_id: formData.get('appointmentSection'),
                        program_id: formData.get('appointmentProgram'),
                        department: formData.get('appointmentDepartment'),
                        appointment_date: formData.get('appointmentDate'),
                        start_time: formData.get('appointmentStartTime'),
                        end_time: formData.get('appointmentEndTime'),
                        reason: formData.get('reason'),
                        notes: formData.get('notes'),
                        status: appointment.status
                    };

                    fetch(`appointment-actions.php?action=update&id=${appointment.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(updatedData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadAppointments();
                            closeAppointmentModal();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Appointment updated successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            // Restore the original submit handler after successful update
                            form.removeEventListener('submit', updateHandler);
                            form.addEventListener('submit', handleAppointmentSubmit);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update appointment: ' + data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating the appointment.'
                        });
                    });
                });
            }

            // Delete appointment function
            function deleteAppointment() {
                const appointmentId = appointments.find(apt => {
                    return document.getElementById('appointmentDetails').innerHTML.includes(apt.id);
                })?.id;

                if (!appointmentId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to find appointment to delete.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`appointment-actions.php?action=delete&id=${appointmentId}`, {
                            method: 'DELETE'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                loadAppointments();
                                closeDetailsModal();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Appointment deleted successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to delete appointment: ' + data.message
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting the appointment.'
                            });
                        });
                    }
                });
            }
        </script>

    <?php include 'admin-profile-modals.php'; ?>
    <script src="../logout.js"></script>
    </body>
</html>
