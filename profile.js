function toggleSection(sectionId) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(sec => sec.style.display = 'none');
    if (sectionId) {
        document.getElementById(sectionId).style.display = 'block';

        if (sectionId === 'chartSection') {
            renderChart(chartType);
        }
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
                data: [bookingCount, emergencyCount],
                backgroundColor: ['#FFBE76', '#FF7979'],
                borderColor: ['#FFA726', '#D84315'],
                borderWidth: 1,
                fill: type !== 'pie'
            }]
        },
        options: {
            responsive: true,
            scales: (type === 'pie') ? {} : {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: {
                    display: type === 'pie' ? true : false
                }
            }
        }
    });
}

function updateChartType() {
    const select = document.getElementById('chartType');
    chartType = select.value;
    renderChart(chartType);
}
window.onload = () => {
    toggleSection('profileSection');
};
