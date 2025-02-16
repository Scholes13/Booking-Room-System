// js/admin/activityreports/activity-charts.js

class ActivityCharts {
    constructor() {
        this.charts = {};
    }

    // Memutuskan chart method berdasarkan reportType
    initializeCharts(data, reportType) {
        // Destroy existing charts
        Object.values(this.charts).forEach(chart => chart.destroy());
        this.charts = {};

        switch (reportType) {
            case 'employee_activity':
                this.initializeEmployeeActivityCharts(data);
                break;
            case 'department_activity':
                this.initializeDepartmentActivityCharts(data);
                break;
        }
    }

    initializeEmployeeActivityCharts(data) {
        // Contoh: menampilkan bar chart total activity per employee
        const ctx = document.getElementById('employeeActivityChart');
        if (!ctx) return;

        this.charts.employeeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.employees.map(emp => emp.name),
                datasets: [{
                    label: 'Activities',
                    data: data.employees.map(emp => emp.activity_count),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Employee Activity Chart'
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    initializeDepartmentActivityCharts(data) {
        // Contoh: menampilkan doughnut chart total activity per department
        const ctx = document.getElementById('departmentActivityChart');
        if (!ctx) return;

        this.charts.deptChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.departments.map(d => d.name),
                datasets: [{
                    data: data.departments.map(d => d.total_activities),
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
                    legend: { position: 'bottom' },
                    title: {
                        display: true,
                        text: 'Department Activity Distribution'
                    }
                }
            }
        });
    }
}

// Inisialisasi di Blade:
// <script src="{{ asset('js/admin/activityreports/activity-charts.js') }}"></script>
// <script>
//   document.addEventListener('DOMContentLoaded', () => {
//       window.activityCharts = new ActivityCharts();
//       // setelah data di-fetch, panggil: activityCharts.initializeCharts(data, reportType);
//   });
// </script>
