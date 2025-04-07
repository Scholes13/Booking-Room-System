document.addEventListener('DOMContentLoaded', function() {
    try {
        // Check if required classes are available
        if (typeof ActivityFilterManagerApp === 'undefined') {
            throw new Error('ActivityFilterManagerApp is not loaded');
        }
        if (typeof ActivityReportGenerator === 'undefined') {
            throw new Error('ActivityReportGenerator class is not loaded');
        }

        // Initialize the filter manager
        const filterManager = ActivityFilterManagerApp.create().init();
        
        // Initialize the report generator with the filter manager
        const reportGenerator = new ActivityReportGenerator(filterManager);
        
        // Connect the report generator to the filter manager
        filterManager.setReportGenerator(reportGenerator);
        
        // Initialize export manager
        const exportManager = new ActivityExportManager(filterManager);
        
        // Set up viewReport button functionality
        const viewReportBtn = document.getElementById('viewReport');
        if (viewReportBtn) {
            viewReportBtn.addEventListener('click', async () => {
                try {
                    // Show loading state
                    const loadingElement = document.getElementById('loading');
                    const reportContentElement = document.getElementById('report_content');
                    
                    if (loadingElement) loadingElement.classList.remove('hidden');
                    if (reportContentElement) reportContentElement.innerHTML = '';
                    
                    // Generate the report
                    const reportContent = await reportGenerator.generateReport();
                    
                    // Hide loading and show the report
                    if (loadingElement) loadingElement.classList.add('hidden');
                    if (reportContentElement) reportContentElement.innerHTML = reportContent;
                } catch (error) {
                    console.error('Error generating report:', error);
                    // Hide loading
                    if (loadingElement) loadingElement.classList.add('hidden');
                    
                    // Show error message
                    if (reportContentElement) {
                        reportContentElement.innerHTML = `
                            <div class="text-center py-12 text-red-500">
                                <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                                <p>Failed to generate report. Please try again or contact support.</p>
                                <p class="text-sm mt-2">${error.message || 'Unknown error'}</p>
                            </div>
                        `;
                    }
                }
            });
        }
        
        // Make instances available globally if needed
        window.activityFilterManager = filterManager;
        window.activityReportGenerator = reportGenerator;
        
        console.log('[ActivityReports] Initialized successfully');
    } catch (error) {
        console.error('Error initializing Activity Reports:', error);
        // Show error to user
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Initialization Error',
                text: 'Failed to initialize activity reports. Please refresh the page or contact support.',
                showConfirmButton: true
            });
        }
    }
}); 