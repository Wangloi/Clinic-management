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

    // Show loading state
    modalContent.innerHTML = '<div class="text-center py-8"><div class="loading inline-block"></div><p class="mt-2">Loading visit data...</p></div>';

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
                modalContent.innerHTML = '<div class="alert alert-danger"><p>Error loading visit data: ' + (data.message || 'Unknown error') + '</p></div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = '<div class="alert alert-danger"><p>Error loading visit data. Please try again.</p></div>';
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

// Enhanced print functionality for entire reports
function printAllReports() {
    // Add loading state
    const printBtn = document.querySelector('.print\\:hidden');
    if (printBtn) {
        printBtn.disabled = true;
        printBtn.innerHTML = '<div class="loading inline-block mr-2"></div>Preparing print...';
    }

    // Create print content
    const printContent = document.querySelector('.space-y-6').innerHTML;
    const styles = document.querySelectorAll('style, link[rel="stylesheet"]');

    let styleContent = '';
    styles.forEach(style => {
        if (style.tagName === 'STYLE') {
            styleContent += style.innerHTML;
        } else if (style.tagName === 'LINK') {
            // For external stylesheets, we'd need to fetch them, but for now we'll use inline styles
            styleContent += '/* External stylesheet: ' + style.href + ' */';
        }
    });

    const printWindow = window.open('', '_blank');
    const currentDate = new Date().toLocaleDateString();
    const currentTime = new Date().toLocaleTimeString();

    printWindow.document.write(`
        <html>
        <head>
            <title>SRCB Clinic - Complete Reports</title>
            <style>${styleContent}</style>
        </head>
        <body>
            <h1 style="text-align: center; margin-bottom: 20px;">SRCB Clinic Management System</h1>
            <h2 style="text-align: center; margin-bottom: 30px;">Complete Reports</h2>
            ${printContent}
            <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
                Generated on: ${currentDate} ${currentTime}
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
        printWindow.print();
        printWindow.close();

        // Reset button state
        if (printBtn) {
            printBtn.disabled = false;
            printBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>Print Reports';
        }
    }, 1000);
}

// Filter functionality for reason stats
function filterReasonStats(month) {
    const form = document.createElement('form');
    form.method = 'GET';
    form.innerHTML = '<input type="hidden" name="reason_month" value="' + month + '">';
    document.body.appendChild(form);
    form.submit();
}

// Export functionality (placeholder for future implementation)
function exportToCSV(sectionId) {
    alert('CSV export functionality will be implemented in the next update.');
}

function exportToPDF(sectionId) {
    alert('PDF export functionality will be implemented in the next update.');
}

// Initialize any interactive elements when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to buttons that make AJAX calls
    const ajaxButtons = document.querySelectorAll('[onclick*="openMonthModal"]');
    ajaxButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.add('loading');
        });
    });

    // Add smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + P for print
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            printAllReports();
        }

        // Escape key handled in modal functions
    });

    // Add tooltips for better UX
    const tooltipElements = document.querySelectorAll('[title]');
    tooltipElements.forEach(element => {
        element.setAttribute('data-original-title', element.getAttribute('title'));
    });
});

// Error handling for fetch requests
function handleFetchError(error, context) {
    console.error(`Error in ${context}:`, error);
    // Could implement user-friendly error notifications here
    showNotification('An error occurred while loading data. Please try again.', 'error');
}

// Notification system (placeholder)
function showNotification(message, type = 'info') {
    // This could be enhanced with a proper notification library
    console.log(`${type.toUpperCase()}: ${message}`);
    // For now, just use alert, but in production you'd want a better notification system
    if (type === 'error') {
        alert('Error: ' + message);
    }
}
