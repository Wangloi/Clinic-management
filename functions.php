<?php
/**
 * Generate the system logs table HTML
 * @param PDO $pdo Database connection
 * @return string HTML table
 */
function generateLogsTable($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT sl.*,
                   CASE
                       WHEN sl.user_type = 'Clinic_Staff' THEN cs.clinic_email
                       WHEN sl.user_type = 'Head_Staff' THEN hs.head_email
                       ELSE 'Unknown'
                   END as user_email
            FROM System_Logs sl
            LEFT JOIN Clinic_Staff cs ON sl.user_type = 'Clinic_Staff' AND sl.user_id = cs.staff_id
            LEFT JOIN Head_Staff hs ON sl.user_type = 'Head_Staff' AND sl.user_id = hs.head_id
            WHERE sl.action != 'view'
            ORDER BY sl.timestamp DESC
        ");
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if there are any login actions
        $hasLoginActions = false;
        foreach ($logs as $log) {
            if (strtolower($log['action']) === 'login') {
                $hasLoginActions = true;
                break;
            }
        }

        if (!empty($logs)) {
            $html = '<div class="bg-white rounded-lg shadow overflow-hidden mx-auto">';
            $html .= '<div class="overflow-x-auto">';
            $html .= '<table class="min-w-full divide-y divide-gray-200 mx-auto">';
            $html .= '<thead class="bg-gray-50">';
            $html .= '<tr>';
            $html .= '<th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>';
            $html .= '<th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Type</th>';
            $html .= '<th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Email</th>';
            $html .= '<th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>';
            $html .= '<th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody class="bg-white divide-y divide-gray-200">';

            foreach ($logs as $log) {
                $html .= '<tr class="hover:bg-gray-50">';
                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . date('M j, Y H:i', strtotime($log['timestamp'])) . '</td>';
                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">';

                if ($log['user_type'] === 'Head_Staff') {
                    $html .= '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Superadmin</span>';
                } else {
                    $html .= '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Clinic Staff</span>';
                }

                $html .= '</td>';
                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">' . htmlspecialchars($log['user_email'] ?? $log['user_id']) . '</td>';
                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">';

                $actionClass = '';
                switch (strtolower($log['action'])) {
                    case 'add':
                        $actionClass = 'bg-green-100 text-green-800';
                        break;
                    case 'edit':
                    case 'update':
                        $actionClass = 'bg-blue-100 text-blue-800';
                        break;
                    case 'delete':
                        $actionClass = 'bg-red-100 text-red-800';
                        break;
                    case 'login':
                        $actionClass = 'bg-purple-100 text-purple-800';
                        break;
                    default:
                        $actionClass = 'bg-gray-100 text-gray-800';
                }

                $html .= '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ' . $actionClass . '">' . htmlspecialchars($log['action']) . '</span>';
                $html .= '</td>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate" title="' . htmlspecialchars($log['details']) . '">' . htmlspecialchars(substr($log['details'], 0, 50)) . (strlen($log['details']) > 50 ? '...' : '') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';

            return $html;
        } else {
            $message = "No logs found";
            $subMessage = "Logs will appear here when actions are performed";

            if (!$hasLoginActions) {
                $message = "No login activities recorded";
                $subMessage = "Login actions will be logged here when users access the system";
            }

            return '<div class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm font-medium">' . $message . '</p>
                <p class="text-gray-400 text-xs mt-1">' . $subMessage . '</p>
            </div>';
        }
    } catch (PDOException $e) {
        return '<div class="text-center py-12">
            <p class="text-gray-500 text-sm font-medium">Unable to load logs</p>
            <p class="text-gray-400 text-xs mt-1">Error: ' . htmlspecialchars($e->getMessage()) . '</p>
        </div>';
    }
}
?>
