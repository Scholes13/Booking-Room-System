class RoomCharts {
    constructor() {
        this.charts = {};
    }

    initialize(data) {
        // Hapus chart yang sudah ada jika ada
        if (this.charts.usage) this.charts.usage.destroy();
        if (this.charts.trend) this.charts.trend.destroy();

        // Inisialisasi chart baru
        this.initializeUsageChart(data);
        this.initializeTrendChart(data);
    }

    initializeUsageChart(data) {
        const ctx = document.getElementById('roomUsageChart');
        if (!ctx) return;

        this.charts.usage = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.rooms.map(room => room.name),
                datasets: [{
                    label: 'Hours Used',
                    data: data.rooms.map(room => room.hours_used),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Room Usage Distribution',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Hours Used'
                        }
                    }
                }
            }
        });
    }

    initializeTrendChart(data) {
        const ctx = document.getElementById('roomTrendChart');
        if (!ctx) return;

        // Menyiapkan data untuk trend chart
        const datasets = data.rooms.map((room, index) => ({
            label: room.name,
            data: room.trend_data.map(item => item.usage),
            borderColor: this.getChartColor(index),
            tension: 0.4,
            fill: false
        }));

        this.charts.trend = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.trend_labels, // ['Week 1', 'Week 2', etc.]
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Room Usage Trends',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Hours Used'
                        }
                    }
                }
            }
        });
    }

    getChartColor(index) {
        const colors = [
            'rgb(59, 130, 246)',  // Blue
            'rgb(16, 185, 129)',  // Green
            'rgb(245, 158, 11)',  // Orange
            'rgb(139, 92, 246)',  // Purple
            'rgb(236, 72, 153)',  // Pink
        ];
        return colors[index % colors.length];
    }

    // Method untuk update chart jika diperlukan
    updateCharts(newData) {
        if (this.charts.usage) {
            this.charts.usage.data.labels = newData.rooms.map(room => room.name);
            this.charts.usage.data.datasets[0].data = newData.rooms.map(room => room.hours_used);
            this.charts.usage.update();
        }

        if (this.charts.trend) {
            this.charts.trend.data.labels = newData.trend_labels;
            this.charts.trend.data.datasets = newData.rooms.map((room, index) => ({
                label: room.name,
                data: room.trend_data.map(item => item.usage),
                borderColor: this.getChartColor(index),
                tension: 0.4,
                fill: false
            }));
            this.charts.trend.update();
        }
    }
}