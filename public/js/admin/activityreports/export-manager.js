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
        this.exportBtn?.addEventListener('click', () => this.showModal());
        this.closeModalBtn?.addEventListener('click', () => this.hideModal());
        this.cancelBtn?.addEventListener('click', () => this.hideModal());
        this.exportModal?.addEventListener('click', (e) => {
            if (e.target === this.exportModal) this.hideModal();
        });
        this.confirmBtn?.addEventListener('click', () => this.handleExport());
        
        // Add event listener for format selection change
        this.formatSelect?.addEventListener('change', () => this.updateFormatDescription());
    }

    showModal() {
        this.exportModal?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Initialize the format description based on the default selected option
        this.updateFormatDescription();
    }

    hideModal() {
        this.exportModal?.classList.add('hidden');
        document.body.style.overflow = '';
    }

    async handleExport() {
        try {
            const params = { 
                ...this.filterManager.getFilterParams(),
                format: this.getSelectedFormat(),
                include_charts: this.includeCharts()
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
            
            this.hideModal();
            
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
}