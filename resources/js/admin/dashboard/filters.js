import { showLoading, hideLoading, setActiveFilter, animateRows, isLoading } from './core';
import { highlightExpiredBookings } from './utils';
import { updateDashboardStats } from './stats';

const filterToday = async () => {
    if (isLoading) return;
    showLoading();
    
    try {
        const today = new Date().toISOString().split('T')[0];
        const rows = Array.from(document.querySelectorAll(".booking-row"));
        
        await animateRows(rows, row => {
            const date = row.querySelector(".booking-date").innerText;
            return date === today;
        });
        
        setActiveFilter('btnToday');
    } finally {
        hideLoading();
    }
}

const filterThisWeek = async () => {
    if (isLoading) return;
    showLoading();
    
    try {
        const today = new Date();
        const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
        const endOfWeek = new Date(today.setDate(startOfWeek.getDate() + 6));
        const rows = Array.from(document.querySelectorAll(".booking-row"));
        
        await animateRows(rows, row => {
            const date = new Date(row.querySelector(".booking-date").innerText);
            return date >= startOfWeek && date <= endOfWeek;
        });
        
        setActiveFilter('btnWeek');
    } finally {
        hideLoading();
    }
}

const filterHour = async () => {
    if (isLoading) return;
    showLoading();
    
    try {
        const now = new Date();
        const currentHour = now.getHours();
        const today = now.toISOString().split('T')[0];
        const rows = Array.from(document.querySelectorAll(".booking-row"));
        
        await animateRows(rows, row => {
            const date = row.querySelector(".booking-date").innerText;
            const startTime = row.querySelector(".booking-time").innerText.split(":");
            const startHour = parseInt(startTime[0]);
            return date === today && startHour === currentHour;
        });
        
        setActiveFilter('btnHour');
    } finally {
        hideLoading();
    }
}

const filterThisMonth = async () => {
    if (isLoading) return;
    showLoading();
    
    try {
        const currentMonth = new Date().getMonth();
        const currentYear = new Date().getFullYear();
        const rows = Array.from(document.querySelectorAll(".booking-row"));
        
        await animateRows(rows, row => {
            const date = new Date(row.querySelector(".booking-date").innerText);
            return date.getMonth() === currentMonth && date.getFullYear() === currentYear;
        });
        
        setActiveFilter('btnMonth');
    } finally {
        hideLoading();
    }
}

const resetFilter = async () => {
    if (isLoading) return;
    showLoading();
    
    try {
        const rows = Array.from(document.querySelectorAll(".booking-row"));
        rows.forEach(row => {
            row.style.display = '';
            row.classList.remove('hidden');
        });
        
        setActiveFilter(null);
        highlightExpiredBookings();
        updateDashboardStats();
    } finally {
        hideLoading();
    }
}

export {
    filterToday,
    filterThisWeek,
    filterHour,
    filterThisMonth,
    resetFilter
};