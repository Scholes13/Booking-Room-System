import { updateDashboardStats } from './stats';
import { highlightExpiredBookings } from './utils';
import { filterToday, filterThisWeek, filterHour, filterThisMonth, resetFilter } from './filters';
import { confirmDelete } from './core';

// Initialize Dashboard
const initializeDashboard = () => {
    // Highlight expired bookings
    highlightExpiredBookings();
    
    // Update initial stats
    updateDashboardStats();
    
    // Setup auto-refresh
    setInterval(() => {
        highlightExpiredBookings();
        updateDashboardStats();
    }, 60000); // Update setiap menit
    
    // Load last active filter
    loadLastFilter();
    
    // Setup delete confirmations
    setupDeleteConfirmations();
}

// Load Last Active Filter
const loadLastFilter = () => {
    const lastFilter = localStorage.getItem('activeFilter');
    if (lastFilter) {
        switch(lastFilter) {
            case 'btnToday':
                filterToday();
                break;
            case 'btnWeek':
                filterThisWeek();
                break;
            case 'btnHour':
                filterHour();
                break;
            case 'btnMonth':
                filterThisMonth();
                break;
        }
    }
}

// Setup Delete Confirmations
const setupDeleteConfirmations = () => {
    document.querySelectorAll('form[action*="delete"]').forEach(form => {
        form.onsubmit = (e) => confirmDelete(e, form);
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeDashboard);

// Export functions for global use (window object)
window.filterToday = filterToday;
window.filterThisWeek = filterThisWeek;
window.filterHour = filterHour;
window.filterThisMonth = filterThisMonth;
window.resetFilter = resetFilter;
window.confirmDelete = confirmDelete;

// Export for module use
export {
    initializeDashboard,
    filterToday,
    filterThisWeek,
    filterHour,
    filterThisMonth,
    resetFilter,
    confirmDelete
};