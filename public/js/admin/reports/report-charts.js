class ReportCharts {
    constructor() {
        this.charts = {};
    }

    initializeCharts(data, reportType) {
        // Destroy existing charts
        Object.values(this.charts).forEach(chart => chart.destroy());
        this.charts = {};

        switch (reportType) {
            case 'bookings':
                this.initializeBookingCharts(data);
                break;
            case 'rooms':
                this.initializeRoomCharts(data);
                break;
            case 'departments':
                this.initializeDepartmentCharts(data);
                break;
        }
    }

    initializeBookingCharts(data) {
        // Bookings per Day Chart
        const bookingsCtx = document.getElementById('bookingsChart');
        if (bookingsCtx) {
            this.charts.bookings = new Chart(bookingsCtx, {
                type: 'line',
                data: {
                    labels: data.daily_stats.map(stat => stat.date),
                    datasets: [{
                        label: 'Number of Bookings',
                        data: data.daily_stats.map(stat => stat.count),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Room Usage Distribution Chart
        const roomsCtx = document.getElementById('roomsChart');
        if (roomsCtx) {
            this.charts.rooms = new Chart(roomsCtx, {
                type: 'doughnut',
                data: {
                    labels: data.room_stats.map(stat => stat.room),
                    datasets: [{
                        data: data.room_stats.map(stat => stat.usage_hours),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(249, 115, 22, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(236, 72, 153, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }

    initializeRoomCharts(data) {
        const usageCtx = document.getElementById('roomUsageChart');
        if (usageCtx) {
            this.charts.roomUsage = new Chart(usageCtx, {
                type: 'bar',
                data: {
                    labels: data.rooms.map(room => room.name),
                    datasets: [{
                        label: 'Total Hours Used',
                        data: data.rooms.map(room => room.total_hours),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Hours'
                            }
                        }
                    }
                }
            });
        }

        const occupancyCtx = document.getElementById('roomOccupancyChart');
        if (occupancyCtx) {
            this.charts.roomOccupancy = new Chart(occupancyCtx, {
                type: 'line',
                data: {
                    labels: data.occupancy_trend.map(item => item.date),
                    datasets: [{
                        label: 'Occupancy Rate (%)',
                        data: data.occupancy_trend.map(item => item.rate),
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Occupancy Rate (%)'
                            }
                        }
                    }
                }
            });
        }
    }

    initializeDepartmentCharts(data) {
        const usageCtx = document.getElementById('departmentUsageChart');
        if (usageCtx) {
            this.charts.departmentUsage = new Chart(usageCtx, {
                type: 'bar',
                data: {
                    labels: data.departments.map(dept => dept.name),
                    datasets: [{
                        label: 'Total Bookings',
                        data: data.departments.map(dept => dept.total_bookings),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Bookings'
                            }
                        }
                    }
                }
            });
        }

        const trendsCtx = document.getElementById('departmentTrendsChart');
        if (trendsCtx) {
            this.charts.departmentTrends = new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: data.trends[0].data.map(item => item.date),
                    datasets: data.departments.map((dept, index) => ({
                        label: dept.name,
                        data: data.trends[index].data.map(item => item.bookings),
                        borderColor: this.getChartColor(index),
                        tension: 0.4
                    }))
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Bookings'
                            }
                        }
                    }
                }
            });
        }
    }

    getChartColor(index) {
        const colors = [
            'rgb(59, 130, 246)',   // Blue
            'rgb(16, 185, 129)',   // Green
            'rgb(249, 115, 22)',   // Orange
            'rgb(139, 92, 246)',   // Purple
            'rgb(236, 72, 153)'    // Pink
        ];
        return colors[index % colors.length];
    }
}