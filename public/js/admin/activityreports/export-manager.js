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
        const params = this.filterManager.getFilterParams();
        params.format = this.formatSelect?.value || 'xlsx';
        params.include_charts = this.includeChartsCheckbox?.checked || false;

        // Handle detailed activity reports separately using our custom exporter
        if (params.report_type === 'detailed_activity') {
            this.hideModal();
            
            // Use the report generator's detailed export method
            if (window.activityReportManagers && window.activityReportManagers.report) {
                await window.activityReportManagers.report.exportDetailedReport(params.format);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Export Failed',
                    text: 'Could not find report generator instance.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
            
            return;
        }

        try {
            this.confirmBtn.disabled = true;
            this.confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';

            const response = await fetch(`${window.location.origin}/admin/activity/export`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(params)
            });

            if (!response.ok) {
                throw new Error('Export failed');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;

            // Tentukan ekstensi berdasarkan format
            let ext = 'xlsx';
            if (params.format === 'pdf') ext = 'pdf';
            if (params.format === 'csv') ext = 'csv';

            // Generate nama file
            const reportType = params.report_type || 'activity';
            const timestamp = Date.now(); // Menggunakan timestamp untuk unik
            a.download = `${reportType}_report_${timestamp}.${ext}`;
            
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            Swal.fire({
                icon: 'success',
                title: 'Export Successful',
                text: 'Your activity report has been exported.',
                timer: 2000,
                showConfirmButton: false
            });
            this.hideModal();
        } catch (error) {
            console.error('Export error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Export Failed',
                text: 'Could not export the activity report. Please try again.',
                timer: 3000,
                showConfirmButton: false
            });
        } finally {
            this.confirmBtn.disabled = false;
            this.confirmBtn.innerHTML = 'Export';
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
}