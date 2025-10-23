<?php
include 'user-role.php';
include 'verifyer.php';
include 'students_data.php';

// Get filters from query parameters
$filters = [
    'search' => $_GET['search'] ?? '',
    'sort' => $_GET['sort'] ?? 'name-asc',
    'department' => $_GET['department'] ?? ''
];

// Pagination settings
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$rows_per_page = isset($_GET['rows']) ? max(1, intval($_GET['rows'])) : 10;
$offset = ($current_page - 1) * $rows_per_page;

// Get data
$total_students = getTotalStudentsCount($filters);
$students = getAllStudents($filters, $rows_per_page, $offset);
$total_pages = ceil($total_students / $rows_per_page);

// Get programs and sections for dropdowns
$programs = getAllPrograms();
$sections = getAllSections();
?>
