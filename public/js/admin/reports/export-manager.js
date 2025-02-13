// export-manager.js
class ExportManager {
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
        // Tombol "Export" -> showModal
        this.exportBtn.addEventListener('click', () => this.showModal());

        // Tombol "Close" (X)
        this.closeModalBtn.addEventListener('click', () => this.hideModal());

        // Tombol "Cancel"
        this.cancelBtn.addEventListener('click', () => this.hideModal());

        // Tombol "Export"
        this.confirmBtn.addEventListener('click', () => this.handleExport());

        // Tutup modal jika klik di luar container
        this.exportModal.addEventListener('click', (e) => {
            if (e.target === this.exportModal) {
                this.hideModal();
            }
        });
    }

    showModal() {
        this.exportModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scroll
    }

    hideModal() {
        this.exportModal.classList.add('hidden');
        document.body.style.overflow = ''; // Restore scroll
    }

    async handleExport() {
        // Ambil parameter filter
        const params = this.filterManager.getFilterParams();
        // Ambil nilai format dan include_charts dari modal
        params.format = this.formatSelect.value;
        params.include_charts = this.includeChartsCheckbox.checked;

        try {
            // Tampilkan state loading di tombol Export
            this.confirmBtn.disabled = true;
            this.confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';

            const response = await fetch(`${window.location.origin}/admin/reports/export`, {
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

            // Download file
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            // Tentukan ekstensi
            let ext = 'xlsx';
            if (params.format === 'pdf') ext = 'pdf';
            if (params.format === 'csv') ext = 'csv';

            a.download = `report_${params.report_type}_${Date.now()}.${ext}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            // Optional: tampilkan notifikasi sukses
            Swal.fire({
                icon: 'success',
                title: 'Export Successful',
                text: 'Your report has been exported.',
                timer: 2000,
                showConfirmButton: false
            });

            // Tutup modal
            this.hideModal();

        } catch (error) {
            console.error('Export error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Export Failed',
                text: 'Could not export the report. Please try again.',
                timer: 3000,
                showConfirmButton: false
            });
        } finally {
            // Kembalikan state tombol
            this.confirmBtn.disabled = false;
            this.confirmBtn.innerHTML = 'Export';
        }
    }
}
