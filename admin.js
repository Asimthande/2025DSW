fetch('admin_chart_data.php')
    .then(response => response.json())
    .then(data => {
        const totalStudents = data.total_students;
        const verifiedStudents = data.students_verified;
        const unverifiedStudents = data.students_unverified;

        const totalDrivers = data.drivers;

        const locationDates = data.location_dates;
        const locationCounts = data.location_counts;

        const totalNotifications = data.notifications;

        document.getElementById('totalStudents').textContent = `Total Students: ${totalStudents}`;

        new Chart(document.getElementById('studentsChart'), {
            type: 'bar',
            data: {
                labels: ['Verified Students', 'Unverified Students'],
                datasets: [{
                    label: 'Verified',
                    data: [verifiedStudents, unverifiedStudents],
                    backgroundColor: ['#36a2eb', '#ff6384'],
                    borderColor: ['#36a2eb', '#ff6384'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        new Chart(document.getElementById('driversChart'), {
            type: 'bar',
            data: {
                labels: ['Drivers'],
                datasets: [{
                    label: 'Driver Count',
                    data: [totalDrivers],
                    backgroundColor: ['#ff9f40'],
                    borderColor: ['#ff9f40'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        new Chart(document.getElementById('locationHistoryChart'), {
            type: 'bar',
            data: {
                labels: locationDates,
                datasets: [{
                    label: 'Location History',
                    data: locationCounts,
                    backgroundColor: '#4bc0c0',
                    borderColor: '#4bc0c0',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        new Chart(document.getElementById('notificationsChart'), {
            type: 'bar',
            data: {
                labels: ['Notifications'],
                datasets: [{
                    label: 'Notification Count',
                    data: [totalNotifications],
                    backgroundColor: '#ffcd56',
                    borderColor: '#ffcd56',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
