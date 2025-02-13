(function(window) {
    'use strict';

    const Dashboard = {
        initialize() {
            this.setupEventListeners();
            this.setupTableSorting();
            this.highlightExpiredBookings();
            this.restoreLastFilter();
            Stats.updateBookingStats();
            this.startAutoRefresh();
        },

        setupEventListeners() {
            // Event untuk tombol filter
            document.getElementById(Constants.FILTERS.TODAY)?.addEventListener('click', () => Filters.filterToday());
            document.getElementById(Constants.FILTERS.WEEK)?.addEventListener('click', () => Filters.filterThisWeek());
            document.getElementById(Constants.FILTERS.HOUR)?.addEventListener('click', () => Filters.filterHour());
            document.getElementById(Constants.FILTERS.MONTH)?.addEventListener('click', () => Filters.filterThisMonth());
            document.getElementById('btnReset')?.addEventListener('click', () => Filters.resetFilter());

            // Event untuk form delete
            document.querySelectorAll('form.delete-form').forEach(form => {
                form.addEventListener('submit', this.handleDelete.bind(this));
            });

            // Event pada window
            window.addEventListener('resize', 
                DashboardUtils.debounce(() => Stats.updateBookingStats(), Constants.DEBOUNCE_DELAY)
            );

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.refreshDashboard();
                }
            });
        },

        setupTableSorting() {
            const table = document.getElementById(Constants.ELEMENTS.TABLE);
            if (!table) return;

            const headers = table.querySelectorAll('th');
            headers.forEach(header => {
                if (header.dataset.sortable !== 'false') {
                    header.addEventListener('click', () => this.handleSort(header));
                }
            });
        },

        async handleSort(header) {
            const table = header.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const index = Array.from(header.parentNode.children).indexOf(header);
            const isNumeric = header.dataset.type === 'number';
            const isDate = header.dataset.type === 'date';

            // Toggle arah sort
            const currentDir = header.dataset.sortDir || 'asc';
            const newDir = currentDir === 'asc' ? 'desc' : 'asc';

            // Update indikator sort
            table.querySelectorAll('th').forEach(th => {
                th.dataset.sortDir = '';
                th.querySelector('.sort-indicator')?.remove();
            });

            header.dataset.sortDir = newDir;
            header.insertAdjacentHTML('beforeend', 
                `<span class="sort-indicator ml-1">${newDir === 'asc' ? '↑' : '↓'}</span>`
            );

            // Sorting baris
            const sortedRows = rows.sort((a, b) => {
                const aVal = a.children[index].textContent.trim();
                const bVal = b.children[index].textContent.trim();

                if (isDate) {
                    return new Date(aVal) - new Date(bVal);
                } else if (isNumeric) {
                    return parseFloat(aVal) - parseFloat(bVal);
                } else {
                    return aVal.localeCompare(bVal);
                }
            });

            if (newDir === 'desc') {
                sortedRows.reverse();
            }

            // Animasi sort
            tbody.style.opacity = '0';
            await new Promise(resolve => setTimeout(resolve, 150));
            
            sortedRows.forEach(row => tbody.appendChild(row));
            
            tbody.style.opacity = '1';
        },

        async handleDelete(event) {
            event.preventDefault();
            const form = event.target;

            const result = await DashboardUtils.confirmDelete();
            if (result.isConfirmed) {
                try {
                    DashboardUtils.showLoading();
                    const response = await fetch(form.action, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                        }
                    });

                    if (!response.ok) throw new Error('Delete failed');

                    const row = form.closest('tr');
                    row.style.opacity = '0';
                    await new Promise(resolve => setTimeout(resolve, 300));
                    row.remove();

                    Stats.updateBookingStats();

                    await Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data booking telah dihapus',
                        icon: 'success',
                        background: '#1f2937',
                        color: '#fff'
                    });
                } catch (error) {
                    console.error('Delete error:', error);
                    await DashboardUtils.showError('Gagal menghapus data');
                } finally {
                    DashboardUtils.hideLoading();
                }
            }
        },

        highlightExpiredBookings() {
            const rows = document.querySelectorAll('.booking-row');
            rows.forEach(row => {
                const endTime = row.getAttribute('data-endtime');
                DashboardUtils.updateTableRow(row, DashboardUtils.isExpired(endTime));
            });
        },

        restoreLastFilter() {
            const lastFilter = StorageManager.get('activeFilter');
            if (lastFilter) {
                switch(lastFilter) {
                    case Constants.FILTERS.TODAY:
                        Filters.filterToday();
                        break;
                    case Constants.FILTERS.WEEK:
                        Filters.filterThisWeek();
                        break;
                    case Constants.FILTERS.HOUR:
                        Filters.filterHour();
                        break;
                    case Constants.FILTERS.MONTH:
                        Filters.filterThisMonth();
                        break;
                }
            }
        },

        startAutoRefresh() {
            setInterval(() => {
                this.highlightExpiredBookings();
                Stats.updateBookingStats();
            }, Constants.REFRESH_INTERVAL);
        },

        async refreshDashboard() {
            this.highlightExpiredBookings();
            await Stats.updateBookingStats();
        }
    };

    // Inisialisasi saat DOM sudah siap
    document.addEventListener('DOMContentLoaded', () => {
        Dashboard.initialize();
    });

    window.Dashboard = Dashboard;

})(window);
