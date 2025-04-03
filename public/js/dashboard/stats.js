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
            
            // --- Update Label Booking Card ---
            Stats.updateBookingLabel(filterType);

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
            let departmentBookings = {}; // Track bookings by department

            bookingRows.forEach(row => {
                const bookingDate = row.querySelector('.booking-date')?.textContent.trim();
                const bookingTime = row.querySelector('.booking-time')?.textContent.trim();
                const bookingEndTime = row.querySelector('.booking-endtime')?.textContent.trim();
                // Asumsikan sel ke-6 berisi nama ruangan dan sel ke-2 berisi department
                const roomCell = row.querySelector('td:nth-child(6)');
                const roomName = roomCell ? roomCell.textContent.trim() : 'Unknown';
                
                const deptCell = row.querySelector('td:nth-child(2)');
                const deptName = deptCell ? deptCell.textContent.trim() : 'Unknown';

                if (bookingDate && bookingTime && bookingEndTime) {
                    const start = new Date(bookingDate + ' ' + bookingTime);
                    const end = new Date(bookingDate + ' ' + bookingEndTime);
                    // Hitung durasi booking dalam jam
                    const duration = (end - start) / (1000 * 60 * 60);
                    if (!isNaN(duration) && duration > 0) {
                        totalBookedHours += duration;
                        roomBookedHours[roomName] = (roomBookedHours[roomName] || 0) + duration;
                        
                        // Track department usage
                        departmentBookings[deptName] = (departmentBookings[deptName] || 0) + 1;
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
                roomUsageEl.textContent = `${usagePercentage}%`;
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
                    ? `${mostUsedRoomName} (${maxHours.toFixed(1)} jam)`
                    : '-';
            }
            
            // --- Determine Top Departments ---
            let topDepartments = [];
            
            // Convert department bookings to array for sorting
            const departmentEntries = Object.entries(departmentBookings);
            
            if (departmentEntries.length > 0) {
                // Sort departments by number of bookings (descending)
                departmentEntries.sort((a, b) => b[1] - a[1]);
                
                // Take top 3 departments
                topDepartments = departmentEntries.slice(0, 3).map(entry => entry[0]);
            }
            
            // Update top departments display
            const topDepartmentsEl = document.getElementById('topDepartments');
            if (topDepartmentsEl) {
                if (topDepartments.length > 0) {
                    topDepartmentsEl.textContent = topDepartments.join(', ');
                } else {
                    topDepartmentsEl.textContent = 'Tidak ada data';
                }
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
        },

        updateBookingLabel(filterType) {
            let labelText = "Today's Bookings"; // default
            switch(filterType) {
                case "today":
                    labelText = "Today's Bookings";
                    break;
                case "week":
                    labelText = "This Week's Bookings";
                    break;
                case "hour":
                    labelText = "This Hour's Bookings";
                    break;
                case "month":
                    labelText = "This Month's Bookings";
                    break;
                default:
                    labelText = "Today's Bookings";
            }
            console.log(`Updating booking label to: ${labelText} based on filter: ${filterType}`);
            const bookingLabelEl = document.getElementById('bookingLabel');
            if (bookingLabelEl) {
                bookingLabelEl.textContent = labelText;
            }
        }
    };

    window.Stats = Stats;
})(window);
