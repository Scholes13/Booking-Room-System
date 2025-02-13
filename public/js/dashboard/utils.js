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
                    return item; // Return sebagai string jika bukan JSON yang valid
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

        confirmDelete() {
            // Menggunakan SweetAlert2 untuk konfirmasi penghapusan
            return Swal.fire({
                title: 'Anda yakin?',
                text: 'Data akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!'
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

        showError(message) {
            return Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error'
            });
        },

        /**
         * Menganimasikan perubahan nilai angka pada sebuah elemen HTML.
         *
         * @param {HTMLElement} element - Elemen untuk menampilkan angka.
         * @param {number} start - Nilai awal.
         * @param {number} end - Nilai akhir.
         * @param {number} duration - Durasi animasi dalam milidetik.
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
