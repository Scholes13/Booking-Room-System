// js/admin/activityreports/export-manager.js

class ActivityExportManager {
    constructor(filterManager) {
        this.filterManager = filterManager;
        this.initializeElements();
        this.initializeEventListeners();
    }

    initializeElements() {
        this.exportBtn = document.getElementById('exportReport');
        this.exportModal = document.getElementById('exportModal');
        this.closeModalBtn = document.getElementById('closeExportModal');
        this.cancelBtn = document.getElementById('cancelExport');
        this.confirmBtn = document.getElementById('confirmExport');
        this.formatSelect = document.getElementById('export_format');
        this.includeChartsCheckbox = document.getElementById('include_charts');
    }

    initializeEventListeners() {
        this.exportBtn?.addEventListener('click', () => this.openExportModal());
        this.closeModalBtn?.addEventListener('click', () => this.closeExportModal());
        this.cancelBtn?.addEventListener('click', () => this.closeExportModal());
        this.exportModal?.addEventListener('click', (e) => {
            if (e.target === this.exportModal) this.closeExportModal();
        });
        this.confirmBtn?.addEventListener('click', () => this.handleExport());
        
        // Add event listener for format selection change
        this.formatSelect?.addEventListener('change', () => this.updateFormatDescription());
    }

    async openExportModal() {
        try {
            // Update filters dari UI
            const filters = this.filterManager.getFilterParams();
            
            // Show modal
            this.exportModal?.classList.remove('hidden');
            
            // Initialize the format description based on the default selected option
            this.updateFormatDescription();
        } catch (error) {
            console.error('Error opening export modal:', error);
        }
    }

    closeExportModal() {
        this.exportModal?.classList.add('hidden');
        document.body.style.overflow = '';
    }

    async handleExport() {
        try {
            const params = { 
                ...this.filterManager.getFilterParams(),
                format: this.getSelectedFormat(),
                include_charts: this.includeCharts(),
                sort_by_date: true
            };

            // Determine the correct prefix based on the URL path
            let prefix = '/admin';
            if (window.location.pathname.includes('/bas/')) {
                prefix = '/bas';
            }

            // Use appropriate endpoint based on report type
            let endpoint = `${prefix}/activity/export`;
            
            console.log(`[ActivityExportManager] Exporting with params:`, params);
            console.log(`[ActivityExportManager] Endpoint: ${endpoint}`);

            // Show loading in modal
            this.setExportButtonLoading(true);
            
            const response = await fetch(`${window.location.origin}${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(params)
            });

            if (!response.ok) {
                throw new Error(`Export failed with status: ${response.status}`);
            }

            // Handle different formats
            const disposition = response.headers.get('Content-Disposition');
            let filename = 'activity-report';
            
            if (disposition && disposition.includes('filename=')) {
                filename = disposition.split('filename=')[1].split(';')[0].trim().replace(/"/g, '');
            } else {
                // Default filename with timestamp
                const date = new Date().toISOString().split('T')[0];
                filename = `activity-report-${date}.${params.format}`;
            }

            // Create a temporary URL for the blob and trigger download
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            
            this.closeExportModal();
            
        } catch (error) {
            console.error('Export error:', error);
            this.showError('Failed to export report. Please try again.');
        } finally {
            this.setExportButtonLoading(false);
        }
    }

    // New method to update the format description
    updateFormatDescription() {
        const format = this.formatSelect?.value || 'xlsx';
        const descriptions = document.querySelectorAll('#export_description p');
        
        // Hide all descriptions
        descriptions.forEach(desc => desc.classList.add('hidden'));
        
        // Show the relevant description
        const activeDesc = document.querySelector(`.${format}_description`);
        if (activeDesc) {
            activeDesc.classList.remove('hidden');
        }
    }

    getSelectedFormat() {
        return this.formatSelect?.value || 'xlsx';
    }

    includeCharts() {
        return this.includeChartsCheckbox?.checked || false;
    }

    setExportButtonLoading(loading) {
        if (loading) {
            this.confirmBtn.disabled = true;
            this.confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';
        } else {
            this.confirmBtn.disabled = false;
            this.confirmBtn.innerHTML = 'Export';
        }
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Export Failed',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }

    // Mengurutkan data berdasarkan tanggal mulai sebelum export
    sortDataByStartDate(data) {
        // Pastikan data adalah array
        if (!Array.isArray(data)) return data;

        return data.sort((a, b) => {
            // Tentukan kolom tanggal mulai berdasarkan struktur data
            let aDate, bDate;

            if (a['Start Date']) {
                // Format export dengan kolom terpisah
                const aDateStr = a['Start Date'];
                const aTimeStr = a['Start Time'] || '00:00';
                aDate = new Date(`${aDateStr} ${aTimeStr}`);
                
                const bDateStr = b['Start Date'];
                const bTimeStr = b['Start Time'] || '00:00';
                bDate = new Date(`${bDateStr} ${bTimeStr}`);
            } 
            else if (a.start_datetime) {
                // Format dengan datetime lengkap
                aDate = new Date(a.start_datetime);
                bDate = new Date(b.start_datetime);
            }
            else {
                // Jika tidak ada kolom tanggal yang bisa digunakan, kembalikan original order
                return 0;
            }

            return aDate - bDate;
        });
    }

    async exportToExcel() {
        try {
            // Get filter params dari FilterManager
            const filters = this.filterManager.getFilterParams();
            
            // Tambahkan format export
            filters.format = 'xlsx';
            
            // Tentukan endpoint API berdasarkan role
            const baseEndpoint = `/${this.filterManager.isBasRole ? 'bas' : 'admin'}/activity/export`;
            
            console.log('Exporting to Excel with params:', filters);
            
            const response = await fetch(baseEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(filters)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}`);
            }
            
            // Get blob dari response
            const blob = await response.blob();
            const downloadUrl = URL.createObjectURL(blob);
            
            // Buat link download dan klik secara programatis
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = `${filters.report_type}_${filters.time_period}_${new Date().toISOString().slice(0, 10)}.xlsx`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            
            this.closeExportModal();
        } catch (error) {
            console.error('Error exporting to Excel:', error);
            alert('Failed to export data. Please try again.');
        }
    }
}