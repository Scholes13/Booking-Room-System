// Highlight Expired Bookings
const highlightExpiredBookings = () => {
    const now = new Date().toISOString().split('T')[0] + ' ' + 
                new Date().toLocaleTimeString('id-ID', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                });
                
    document.querySelectorAll(".booking-row").forEach(row => {
        const endTime = row.getAttribute("data-endtime");
        if (endTime && endTime < now) {
            row.classList.add("expired-booking");
        } else {
            row.classList.remove("expired-booking");
        }
    });
}

// Date Formatting Utilities
const formatDate = (date) => {
    return date.toISOString().split('T')[0];
}

const formatTime = (date) => {
    return date.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

export {
    highlightExpiredBookings,
    formatDate,
    formatTime
};