(function(window) {
    'use strict';

    const Constants = {
        REFRESH_INTERVAL: 60000, // 1 menit
        DEBOUNCE_DELAY: 250,
        
        ELEMENTS: {
            DASHBOARD: 'dashboard',
            TABLE: 'bookingTable',
            LOADING_OVERLAY: 'loadingOverlay',
            TODAY_BOOKINGS: 'todayBookings',
            ROOM_USAGE: 'roomUsage',
            NEXT_BOOKING: 'nextBookingInfo',
            MOST_USED_ROOM: 'mostUsedRoom'
        },
        
        CLASSES: {
            ACTIVE: 'active',
            HIDDEN: 'hidden',
            EXPIRED: 'expired-booking',
            LOADING: 'loading'
        },
        
        FILTERS: {
            TODAY: 'btnToday',
            WEEK: 'btnWeek',
            HOUR: 'btnHour',
            MONTH: 'btnMonth'
        }
    };

    // Assign ke window
    window.Constants = Constants;

})(window);