function showOverview() {
    document.getElementById('overview').classList.remove('hidden');
    document.getElementById('users').classList.add('hidden');

    document.getElementById('overviewBtn').classList.add('bg-white');
    document.getElementById('usersBtn').classList.remove('bg-white');

    document.getElementById('overviewBtn').classList.remove('cursor-pointer');
    document.getElementById('usersBtn').classList.add('cursor-pointer');
}

function showUsers() {
    document.getElementById('users').classList.remove('hidden');
    document.getElementById('overview').classList.add('hidden');

    document.getElementById('usersBtn').classList.add('bg-white');
    document.getElementById('overviewBtn').classList.remove('bg-white');

    document.getElementById('usersBtn').classList.remove('cursor-pointer');
    document.getElementById('overviewBtn').classList.add('cursor-pointer');
}

document.addEventListener("DOMContentLoaded", function () {

    if (typeof usersByRoleData !== 'undefined') {
        const ctxUsers = document.getElementById('usersByRoleChart').getContext('2d');
        new Chart(ctxUsers, {
            type: 'doughnut',
            data: {
                labels: Object.keys(usersByRoleData),
                datasets: [{
                    label: 'Users by Role', 
                    data: Object.values(usersByRoleData),
                    backgroundColor: ['#4B5563', '#10B981', '#3B82F6'],
                    borderWidth: 1
                }]
            }, 
            options: {
                responsive: true, 
                plugins: {
                    legend: {position: 'bottom'},
                    title: {
                        display: true, 
                        text: 'Users by Role'
                    }
                }
            }
        });
    } else {
        console.error("usersByRoleData is not defined");
    }

    if (typeof appointmentStatusData !== 'undefined') {
        const appointmentsCtx = document.getElementById('appointmentStatusByChart').getContext('2d');
        new Chart(appointmentsCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(appointmentStatusData),
                datasets: [{
                    label: 'Appointments',
                    data: Object.values(appointmentStatusData),
                    backgroundColor: ['#facc15', '#34d399', '#f87171'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: {
                        display: true,
                        text: 'Appointments by Status'
                    }
                }
            }
        });
    } else {
        console.error("appointmentStatusData is not defined");
    }

    const casesCtx = document.getElementById('caseStatusChart').getContext('2d');
    new Chart(casesCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(caseStatusData),
            datasets: [{
                label: 'Cases',
                data: Object.values(caseStatusData),
                backgroundColor: ['#60a5fa', '#fbbf24', '#4ade80'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: {
                    display: true,
                    text: 'Cases by Status'
                }
            }
        }
    });

    //Active vs. Inactive chart

    const userStatusctx = document.getElementById('userStatusChart').getContext('2d');

    new Chart(userStatusctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(userStatusData),
            datasets: [{
                label: 'User Status',
                data: Object.values(userStatusData),
                backgroundColor: ['#4ade80', '#f87171'],
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });


    //case per lawyer chart
    const lawCaseCtx = document.getElementById('lawCaseChart').getContext('2d');

    new Chart(lawCaseCtx, {
        type: 'bar',
        data: {
            labels: lawyerNames,
            datasets: [{
                label: 'Number of Cases',
                data: caseCounts,
                backgroundColor: '#60a5fa',
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            }, 
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});