<?php
include 'connection.php';

// Function to get total accounts count based on filters
function getTotalAccountsCount($filters) {
    global $pdo;

    $sql = "SELECT COUNT(*) FROM (
        SELECT clinic_email as email, 'Clinic Staff' as role, clinic_Fname as fname, clinic_Lname as lname
        FROM Clinic_Staff
        UNION ALL
        SELECT head_email as email, 'Superadmin' as role, head_Fname as fname, head_Lname as lname
        FROM Head_Staff
    ) AS accounts WHERE 1=1";

    $params = [];

    if (!empty($filters['search'])) {
        $sql .= " AND (fname LIKE ? OR lname LIKE ? OR email LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if (!empty($filters['role'])) {
        $sql .= " AND role = ?";
        $params[] = $filters['role'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

// Function to get all accounts with filters, pagination
function getAllAccounts($filters, $limit, $offset) {
    global $pdo;

    $sql = "SELECT * FROM (
        SELECT clinic_email as email, 'Clinic Staff' as role, clinic_Fname as fname, clinic_Lname as lname, staff_id as id
        FROM Clinic_Staff
        UNION ALL
        SELECT head_email as email, 'Superadmin' as role, head_Fname as fname, head_Lname as lname, head_id as id
        FROM Head_Staff
    ) AS accounts WHERE 1=1";

    $params = [];

    if (!empty($filters['search'])) {
        $sql .= " AND (fname LIKE ? OR lname LIKE ? OR email LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if (!empty($filters['role'])) {
        $sql .= " AND role = ?";
        $params[] = $filters['role'];
    }

    // Sorting
    switch ($filters['sort']) {
        case 'name-desc':
            $sql .= " ORDER BY lname DESC, fname DESC";
            break;
        case 'email-asc':
            $sql .= " ORDER BY email ASC";
            break;
        case 'email-desc':
            $sql .= " ORDER BY email DESC";
            break;
        case 'role-asc':
            $sql .= " ORDER BY role ASC";
            break;
        case 'role-desc':
            $sql .= " ORDER BY role DESC";
            break;
        case 'name-asc':
        default:
            $sql .= " ORDER BY lname ASC, fname ASC";
            break;
    }

    $sql .= " LIMIT ? OFFSET ?";
    $params[] = (int)$limit;
    $params[] = (int)$offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Helper function to get full account name
function getAccountFullName($account) {
    return $account['fname'] . ' ' . $account['lname'];
}

// Function to get accounts data with filters and pagination
function getAccountsData($get_params) {
    // Get filters from query parameters
    $filters = [
        'search' => $get_params['search'] ?? '',
        'sort' => $get_params['sort'] ?? 'name-asc',
        'role' => $get_params['role'] ?? ''
    ];

    // Pagination settings
    $current_page = isset($get_params['page']) ? max(1, intval($get_params['page'])) : 1;
    $rows_per_page = isset($get_params['rows']) ? max(1, intval($get_params['rows'])) : 10;
    $offset = ($current_page - 1) * $rows_per_page;

    // Get data
    $total_accounts = getTotalAccountsCount($filters);
    $accounts = getAllAccounts($filters, $rows_per_page, $offset);
    $total_pages = ceil($total_accounts / $rows_per_page);

    // Fix pagination: adjust current_page if it exceeds total_pages
    if ($total_pages == 0) {
        $current_page = 1;
        $offset = 0;
        // Refetch if needed, but since total=0, accounts is empty
    } elseif ($current_page > $total_pages) {
        $current_page = $total_pages;
        $offset = ($current_page - 1) * $rows_per_page;
        // Refetch accounts with corrected offset
        $accounts = getAllAccounts($filters, $rows_per_page, $offset);
    }

    return [
        'filters' => $filters,
        'current_page' => $current_page,
        'rows_per_page' => $rows_per_page,
        'total_accounts' => $total_accounts,
        'accounts' => $accounts,
        'total_pages' => $total_pages
    ];
}
?>
