// State Management
let activeFilter = null;
let isLoading = false;

// UI Loading Management
const showLoading = () => {
    document.getElementById('loadingOverlay').classList.remove('hidden');
    isLoading = true;
}

const hideLoading = () => {
    document.getElementById('loadingOverlay').classList.add('hidden');
    isLoading = false;
}

// Filter Management
const setActiveFilter = (filterId) => {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    if (filterId) {
        document.getElementById(filterId)?.classList.add('active');
    }
    activeFilter = filterId;
    localStorage.setItem('activeFilter', filterId || '');
}

// Row Animation
const animateRows = async (rows, filterFn) => {
    const animations = rows.map(async row => {
        const shouldShow = filterFn(row);
        
        if (!shouldShow) {
            row.classList.add('hidden');
            await new Promise(resolve => setTimeout(resolve, 50));
            row.style.display = 'none';
        } else {
            row.style.display = '';
            row.classList.remove('hidden');
        }
    });
    
    await Promise.all(animations);
    updateDashboardStats();
}

// Delete Confirmation
const confirmDelete = (event, formElement) => {
    event.preventDefault();
    
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data booking akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        background: '#1f2937',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            formElement.submit();
        }
    });
}

export {
    showLoading,
    hideLoading,
    setActiveFilter,
    animateRows,
    confirmDelete,
    isLoading
};