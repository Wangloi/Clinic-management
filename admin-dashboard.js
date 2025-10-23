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
