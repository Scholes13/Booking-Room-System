// Stats Update
const updateDashboardStats = () => {
    const rows = document.querySelectorAll(".booking-row");
    const today = new Date().toISOString().split('T')[0];
    
    const stats = calculateStats(rows, today);
    updateUI(stats);
}

// Calculate Statistics
const calculateStats = (rows, today) => {
    let stats = {
        todayCount: 0,
        nextBooking: null,
        rooms: new Map(),
        departments: new Map(),
        weeklyCount: 0
    };
    
    const currentTime = new Date();
    const startOfWeek = new Date();
    startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());
    
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const date = row.querySelector(".booking-date").innerText;
            const startTime = row.querySelector(".booking-time").innerText;
            const department = row.querySelector("td:nth-child(2)").innerText;
            const room = row.querySelector("td:nth-child(6)").innerText;
            
            processDailyStats(stats, date, startTime, room, today, currentTime);
            processWeeklyStats(stats, date, startOfWeek);
            processRoomStats(stats, room);
            processDepartmentStats(stats, department);
        }
    });
    
    return stats;
}

// Process Different Types of Stats
const processDailyStats = (stats, date, startTime, room, today, currentTime) => {
    if (date === today) {
        stats.todayCount++;
        
        const bookingTime = new Date(`${date} ${startTime}`);
        if (bookingTime > currentTime && (!stats.nextBooking || bookingTime < stats.nextBooking.time)) {
            stats.nextBooking = { time: bookingTime, room: room };
        }
    }
}

const processWeeklyStats = (stats, date, startOfWeek) => {
    const bookingDate = new Date(date);
    if (bookingDate >= startOfWeek) {
        stats.weeklyCount++;
    }
}

const processRoomStats = (stats, room) => {
    stats.rooms.set(room, (stats.rooms.get(room) || 0) + 1);
}

const processDepartmentStats = (stats, department) => {
    stats.departments.set(department, (stats.departments.get(department) || 0) + 1);
}

// Update UI Elements
const updateUI = (stats) => {
    updateTodayCard(stats);
    updateRoomUsageCard(stats);
    updateWeeklyCard(stats);
    updateDepartmentCard(stats);
}

const updateTodayCard = (stats) => {
    document.getElementById('todayBookings').textContent = stats.todayCount;
    document.getElementById('nextBookingInfo').textContent = stats.nextBooking 
        ? `Booking berikutnya: ${stats.nextBooking.time.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} di ${stats.nextBooking.room}`
        : 'Tidak ada booking berikutnya';
}

const updateRoomUsageCard = (stats) => {
    const [mostUsedRoom, maxUsage] = [...stats.rooms.entries()].reduce((max, [room, count]) => 
        count > max[1] ? [room, count] : max, ['', 0]);
    const usagePercentage = Math.round((maxUsage / 10) * 100);
    
    document.getElementById('roomUsage').textContent = `${usagePercentage}%`;
    document.getElementById('mostUsedRoom').textContent = `${mostUsedRoom || '-'}`;
}

const updateWeeklyCard = (stats) => {
    document.getElementById('weeklyBookings').textContent = stats.weeklyCount;
    document.getElementById('weeklyTrend').textContent = `Total booking minggu ini`;
}

const updateDepartmentCard = (stats) => {
    const [topDept, maxBookings] = [...stats.departments.entries()].reduce((max, [dept, count]) => 
        count > max[1] ? [dept, count] : max, ['', 0]);
    
    document.getElementById('activeDepts').textContent = stats.departments.size;
    document.getElementById('topDepartment').textContent = topDept 
        ? `Departemen teraktif: ${topDept}` 
        : 'Belum ada departemen aktif';
}

export { updateDashboardStats };