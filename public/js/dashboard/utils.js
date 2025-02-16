(function(window) {
    'use strict';

    // StorageManager untuk mengelola localStorage
    const StorageManager = {
        set(key, value) {
            try {
                const stringValue = typeof value === 'string' ? value : JSON.stringify(value);
                localStorage.setItem(key, stringValue);
            } catch (e) {
                console.warn('Error saving to localStorage:', e);
            }
        },

        get(key, defaultValue = null) {
            try {
                const item = localStorage.getItem(key);
                if (!item) return defaultValue;
                
                try {
                    return JSON.parse(item);
                } catch {
                    // Jika parsing JSON gagal, kembalikan sebagai string
                    return item;
                }
            } catch (e) {
                console.warn('Error reading from localStorage:', e);
                return defaultValue;
            }
        },

        remove(key) {
            try {
                localStorage.removeItem(key);
            } catch (e) {
                console.warn('Error removing from localStorage:', e);
            }
        }
    };

    // DashboardUtils berisi fungsi-fungsi utilitas untuk dashboard
    const DashboardUtils = {
        debounce(fn, delay) {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => fn.apply(this, args), delay);
            };
        },

        /**
         * Konfirmasi penghapusan dengan SweetAlert2
         * - Warna tombol disesuaikan (merah & abu-abu)
         * - Latar belakang popup pakai Tailwind Gray-100 (#f3f4f6)
         * - Warna teks pakai Tailwind Gray-800 (#111827)
         */
        confirmDelete() {
            return Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',     // Tombol konfirmasi: merah
                cancelButtonColor: '#6b7280',   // Tombol batal: abu-abu
                background: '#f3f4f6',          // Tailwind Gray-100
                color: '#111827'               // Tailwind Gray-800
            });
        },

        showLoading() {
            // Tampilkan overlay loading, pastikan ada elemen dengan id "loadingOverlay"
            document.getElementById('loadingOverlay')?.classList.remove('hidden');
        },

        hideLoading() {
            document.getElementById('loadingOverlay')?.classList.add('hidden');
        },

        updateTableRow(row, expired) {
            if (expired) {
                row.classList.add('expired');
            } else {
                row.classList.remove('expired');
            }
        },

        isExpired(endTime) {
            return new Date(endTime) < new Date();
        },

        /**
         * Menampilkan pesan error dengan SweetAlert2
         * - Disesuaikan warna tombol, background, dll.
         */
        showError(message) {
            return Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error',
                confirmButtonColor: '#d33',
                background: '#f3f4f6',
                color: '#111827'
            });
        },

        /**
         * Menganimasikan perubahan nilai angka pada sebuah elemen HTML.
         */
        animateValue(element, start, end, duration) {
            let startTime = null;

            const step = (timestamp) => {
                if (!startTime) startTime = timestamp;
                const progress = timestamp - startTime;
                // Hitung nilai saat ini berdasarkan progress
                const current = Math.floor(start + (end - start) * (progress / duration));
                element.textContent = current;
                if (progress < duration) {
                    window.requestAnimationFrame(step);
                } else {
                    element.textContent = end;
                }
            };

            window.requestAnimationFrame(step);
        }
    };

    // Menempatkan objek ke global scope agar dapat diakses di file lain
    window.StorageManager = StorageManager;
    window.DashboardUtils = DashboardUtils;

})(window);
