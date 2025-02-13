(function(window) {
    'use strict';

    const Filters = {
        activeFilter: null,
        isLoading: false,

        async filterToday() {
            if (this.isLoading) return;
            
            try {
                this.showLoading();
                const today = new Date().toISOString().split('T')[0];
                await this.filterRows(row => {
                    const date = row.querySelector('.booking-date').innerText;
                    return date === today;
                });
                this.setActiveFilter(Constants.FILTERS.TODAY);
            } finally {
                this.hideLoading();
            }
        },

        async filterThisWeek() {
            if (this.isLoading) return;
            
            try {
                this.showLoading();
                const today = new Date();
                const startOfWeek = this.getStartOfWeek(today);
                const endOfWeek = this.getEndOfWeek(today);
                
                await this.filterRows(row => {
                    const date = new Date(row.querySelector('.booking-date').innerText);
                    return date >= startOfWeek && date <= endOfWeek;
                });
                this.setActiveFilter(Constants.FILTERS.WEEK);
            } finally {
                this.hideLoading();
            }
        },

        async filterHour() {
            if (this.isLoading) return;
            
            try {
                this.showLoading();
                const now = new Date();
                const currentHour = now.getHours();
                const today = now.toISOString().split('T')[0];
                
                await this.filterRows(row => {
                    const date = row.querySelector('.booking-date').innerText;
                    const startTime = row.querySelector('.booking-time').innerText.split(':');
                    const startHour = parseInt(startTime[0]);
                    return date === today && startHour === currentHour;
                });
                this.setActiveFilter(Constants.FILTERS.HOUR);
            } finally {
                this.hideLoading();
            }
        },

        async filterThisMonth() {
            if (this.isLoading) return;
            
            try {
                this.showLoading();
                const now = new Date();
                const currentMonth = now.getMonth();
                const currentYear = now.getFullYear();
                
                await this.filterRows(row => {
                    const date = new Date(row.querySelector('.booking-date').innerText);
                    return date.getMonth() === currentMonth && date.getFullYear() === currentYear;
                });
                this.setActiveFilter(Constants.FILTERS.MONTH);
            } finally {
                this.hideLoading();
            }
        },

        async resetFilter() {
            if (this.isLoading) return;
            
            try {
                this.showLoading();
                const rows = document.querySelectorAll('.booking-row');
                rows.forEach(row => {
                    row.style.display = '';
                    row.classList.remove(Constants.CLASSES.HIDDEN);
                });
                
                this.setActiveFilter(null);
                // Jika perlu, panggil fungsi lain untuk mereset tampilan dashboard
                DashboardUtils.highlightExpiredBookings();
                Stats.updateBookingStats();
            } finally {
                this.hideLoading();
            }
        },

        setActiveFilter(filterId) {
            // Hapus kelas aktif dari semua tombol filter
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove(Constants.CLASSES.ACTIVE);
            });
            
            // Tambahkan kelas aktif ke tombol yang bersangkutan
            if (filterId) {
                const activeButton = document.getElementById(filterId);
                if (activeButton) {
                    activeButton.classList.add(Constants.CLASSES.ACTIVE);
                }
            }
            
            // Perbarui activeFilter dan simpan ke local storage
            this.activeFilter = filterId;
            StorageManager.set('activeFilter', filterId);
            
            // Panggil langsung update statistik agar judul dan data langsung diperbarui
            if (window.Stats && typeof window.Stats.updateBookingStats === 'function') {
                window.Stats.updateBookingStats();
            }
        },

        async filterRows(filterFn) {
            const rows = Array.from(document.querySelectorAll('.booking-row'));
            const animations = rows.map(async row => {
                const shouldShow = filterFn(row);
                
                if (!shouldShow) {
                    row.classList.add(Constants.CLASSES.HIDDEN);
                    await new Promise(resolve => setTimeout(resolve, 50));
                    row.style.display = 'none';
                } else {
                    row.style.display = '';
                    row.classList.remove(Constants.CLASSES.HIDDEN);
                }
            });
            
            await Promise.all(animations);
            Stats.updateBookingStats();
        },

        showLoading() {
            this.isLoading = true;
            DashboardUtils.showLoading();
        },

        hideLoading() {
            this.isLoading = false;
            DashboardUtils.hideLoading();
        },

        getStartOfWeek(date) {
            const d = new Date(date);
            const day = d.getDay();
            const diff = d.getDate() - day + (day === 0 ? -6 : 1);
            return new Date(d.setDate(diff));
        },

        getEndOfWeek(date) {
            const d = new Date(date);
            const day = d.getDay();
            const diff = d.getDate() - day + (day === 0 ? 0 : 7);
            return new Date(d.setDate(diff));
        }
    };

    window.Filters = Filters;

})(window);
