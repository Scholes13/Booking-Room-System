(function(window) {
    'use strict';

    const Stats = {
        updateBookingStats() {
            let bookingRows;
            let filterType = "today"; // default filter

            // Jika ada filter aktif, gunakan baris yang terlihat (hasil filter)
            if (window.Filters && window.Filters.activeFilter) {
                // Tentukan tipe filter berdasarkan ID filter yang aktif
                // Misalnya, jika window.Filters.activeFilter bernilai Constants.FILTERS.WEEK, maka filterType = "week"
                const active = window.Filters.activeFilter;
                if (active === Constants.FILTERS.TODAY) {
                    filterType = "today";
                } else if (active === Constants.FILTERS.WEEK) {
                    filterType = "week";
                } else if (active === Constants.FILTERS.HOUR) {
                    filterType = "hour";
                } else if (active === Constants.FILTERS.MONTH) {
                    filterType = "month";
                }
                // Gunakan baris booking yang masih terlihat (hasil filter)
                bookingRows = Array.from(document.querySelectorAll('.booking-row')).filter(row => {
                    return row.style.display !== 'none';
                });
            } else {
                // Default: hanya booking pada hari ini
                filterType = "today";
                const todayStr = new Date().toISOString().split('T')[0];
                bookingRows = Array.from(document.querySelectorAll('.booking-row')).filter(row => {
                    const bookingDate = row.querySelector('.booking-date')?.textContent.trim();
                    return bookingDate === todayStr;
                });
            }

            // --- Update Judul Booking Secara Dinamis ---
            Stats.updateBookingTitle(filterType);

            // --- Update Booking Count (Jumlah Booking) ---
            const todayBookingsEl = document.getElementById('todayBookings');
            const currentBookings = todayBookingsEl ? parseInt(todayBookingsEl.textContent) || 0 : 0;
            const newBookings = bookingRows.length;
            if (todayBookingsEl) {
                // Animasi perubahan angka menggunakan fungsi animateValue dari DashboardUtils
                DashboardUtils.animateValue(todayBookingsEl, currentBookings, newBookings, 1000);
            }

            // --- Hitung Total Jam Booking dan Akumulasi per Ruangan ---
            let totalBookedHours = 0;
            let roomBookedHours = {};

            bookingRows.forEach(row => {
                const bookingDate = row.querySelector('.booking-date')?.textContent.trim();
                const bookingTime = row.querySelector('.booking-time')?.textContent.trim();
                const bookingEndTime = row.querySelector('.booking-endtime')?.textContent.trim();
                // Asumsikan sel ke-6 berisi nama ruangan
                const roomCell = row.querySelector('td:nth-child(6)');
                const roomName = roomCell ? roomCell.textContent.trim() : 'Unknown';

                if (bookingDate && bookingTime && bookingEndTime) {
                    const start = new Date(bookingDate + ' ' + bookingTime);
                    const end = new Date(bookingDate + ' ' + bookingEndTime);
                    // Hitung durasi booking dalam jam
                    const duration = (end - start) / (1000 * 60 * 60);
                    if (!isNaN(duration) && duration > 0) {
                        totalBookedHours += duration;
                        roomBookedHours[roomName] = (roomBookedHours[roomName] || 0) + duration;
                    }
                }
            });

            // --- Hitung Usage Percentage ---
            // Misalnya: Total ruangan = 3 dan tiap ruangan tersedia 10 jam per hari
            const totalRooms = 3;
            const availableHoursPerRoom = 10;
            const totalAvailableHours = totalRooms * availableHoursPerRoom;
            const usagePercentage = totalAvailableHours > 0
                ? Math.round((totalBookedHours / totalAvailableHours) * 100)
                : 0;

            // Update elemen roomUsage (menampilkan Usage percentage saja)
            const roomUsageEl = document.getElementById('roomUsage');
            if (roomUsageEl) {
                roomUsageEl.textContent = `Usage: ${usagePercentage}%`;
            }

            // --- Tentukan Ruangan Terbanyak ---
            let mostUsedRoomName = '';
            let maxHours = 0;
            for (const room in roomBookedHours) {
                if (roomBookedHours[room] > maxHours) {
                    maxHours = roomBookedHours[room];
                    mostUsedRoomName = room;
                }
            }
            const mostUsedRoomEl = document.getElementById('mostUsedRoom');
            if (mostUsedRoomEl) {
                mostUsedRoomEl.textContent = mostUsedRoomName
                    ? `Ruangan terbanyak: ${mostUsedRoomName} (${maxHours.toFixed(1)} jam)`
                    : '-';
            }
        },

        updateBookingTitle(filterType) {
            let titleText = "Booking Hari Ini"; // default
            switch(filterType) {
                case "today":
                    titleText = "Booking Hari Ini";
                    break;
                case "week":
                    titleText = "Booking Minggu Ini";
                    break;
                case "hour":
                    titleText = "Booking Jam Ini";
                    break;
                case "month":
                    titleText = "Booking Bulan Ini";
                    break;
                default:
                    titleText = "Booking Hari Ini";
            }
            const bookingTitleEl = document.getElementById('bookingTitle');
            if (bookingTitleEl) {
                bookingTitleEl.textContent = titleText;
            }
        }
    };

    window.Stats = Stats;
})(window);
