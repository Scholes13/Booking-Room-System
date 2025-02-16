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

            // Hanya form.delete-form dalam #bookingTable yang terikat event delete
            document.querySelectorAll('#bookingTable form.delete-form').forEach(form => {
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

        // Bagian penting: handleDelete
        async handleDelete(event) {
            event.preventDefault();
            const form = event.target;

            // Konfirmasi hapus (SweetAlert dengan warna latar & tombol disesuaikan)
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

                    // Hapus baris di tabel
                    const row = form.closest('tr');
                    row.style.opacity = '0';
                    await new Promise(resolve => setTimeout(resolve, 300));
                    row.remove();

                    // Update stats
                    Stats.updateBookingStats();

                    // Notifikasi sukses (warna lebih ramah)
                    await Swal.fire({
                        title: 'Data Terhapus',
                        text: 'Booking berhasil dihapus dari sistem.',
                        icon: 'success',
                        confirmButtonColor: '#3b82f6', // Tailwind Blue-500
                        background: '#f3f4f6',         // Tailwind Gray-100
                        color: '#111827'              // Tailwind Gray-800
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
