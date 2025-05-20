
    function toggleSection(sectionId) {
        const sections = document.querySelectorAll('.section');
        sections.forEach(sec => sec.style.display = 'none');
        if (sectionId) {
            document.getElementById(sectionId).style.display = 'block';
        }
    }

    let chartType = 'bar';
    const ctx = document.getElementById('bookingChart').getContext('2d');
    let chart;

    function renderChart(type) {
        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: type,
            data: {
                labels: ['Total Bookings', 'Total Emergencies'],
                datasets: [{
                    label: 'User Activity',
                    data: [<?= $booking_count ?>, <?= $emergency_count ?>],
                    backgroundColor: ['#ffa726', '#ef5350'],
                    borderColor: ['#fb8c00', '#e53935'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: type !== 'pie'
                    }
                },
                scales: type === 'pie' ? {} : {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function updateChartType() {
        chartType = document.getElementById('chartType').value;
        renderChart(chartType);
    }

    window.onload = function () {
        toggleSection('profileSection');
        renderChart('bar');
    };