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
    }

    showModal() {
        this.exportModal?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    hideModal() {
        this.exportModal?.classList.add('hidden');
        document.body.style.overflow = '';
    }

    async handleExport() {
        const params = this.filterManager.getFilterParams();
        params.format = this.formatSelect?.value || 'xlsx';
        params.include_charts = this.includeChartsCheckbox?.checked || false;

        try {
            this.confirmBtn.disabled = true;
            this.confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';

            // Perbaikan endpoint sesuai dengan route Laravel
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

            // Perbaikan nama file dengan menambahkan report type
            const reportType = params.report_type || 'activity';
            const timestamp = new Date().toISOString().split('T')[0];
            a.download = `${reportType}_report_${timestamp}.${params.format}`;
            
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
}

// Pastikan untuk menambahkan di view:
/*
@push('scripts')
    <script src="{{ asset('js/admin/activityreports/export-manager.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const activityExportManager = new ActivityExportManager(window.activityFilterManager);
        });
    </script>
@endpush
*/