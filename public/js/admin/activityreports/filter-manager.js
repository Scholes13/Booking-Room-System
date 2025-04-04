/**
 * ActivityFilterManagerApp
 * ------------------------
 * Filter manager khusus untuk Activity Reports.
 * Hanya punya 2 opsi: employee_activity, department_activity
 * Serta time_period (monthly, quarterly, yearly).
 */

const ActivityFilterManagerApp = (function() {
    class ActivityFilterManager {
        constructor() {
            this.initializeElements();
            this.initializeEventListeners();
            this.updatePeriodSelector(); // Generate period selector on load
        }

        initializeElements() {
            // Pastikan di partial filter-card, ada element:
            // <select id="report_type">, <select id="time_period">, <div id="period_container">
            this.reportType = document.getElementById('report_type');
            this.timePeriod = document.getElementById('time_period');
            this.periodContainer = document.getElementById('period_container');
        }

        initializeEventListeners() {
            if (this.timePeriod) {
                this.timePeriod.addEventListener('change', () => this.updatePeriodSelector());
            }
        }

        updatePeriodSelector() {
            if (!this.periodContainer) return;
            
            const periodType = this.timePeriod.value;
            let html = '';

            switch (periodType) {
                case 'monthly':
                    html = this.createMonthlySelector();
                    break;
                case 'quarterly':
                    html = this.createQuarterlySelector();
                    break;
                case 'yearly':
                    html = this.createYearlySelector();
                    break;
            }

            this.periodContainer.innerHTML = html;
        }

        createMonthlySelector() {
            const currentYear = new Date().getFullYear();
            const currentMonth = new Date().getMonth();
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            return `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Month & Year</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select id="month" class="rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                            ${months.map((month, index) => `
                                <option value="${index + 1}" ${index === currentMonth ? 'selected' : ''}>
                                    ${month}
                                </option>
                            `).join('')}
                        </select>
                        <select id="year" class="rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                            ${this.generateYearOptions(currentYear)}
                        </select>
                    </div>
                </div>
            `;
        }

        createQuarterlySelector() {
            const currentYear = new Date().getFullYear();
            const currentQuarter = Math.floor(new Date().getMonth() / 3) + 1;
            
            return `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Quarter & Year</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select id="quarter" class="rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                            <option value="1" ${currentQuarter === 1 ? 'selected' : ''}>Q1 (Jan-Mar)</option>
                            <option value="2" ${currentQuarter === 2 ? 'selected' : ''}>Q2 (Apr-Jun)</option>
                            <option value="3" ${currentQuarter === 3 ? 'selected' : ''}>Q3 (Jul-Sep)</option>
                            <option value="4" ${currentQuarter === 4 ? 'selected' : ''}>Q4 (Oct-Dec)</option>
                        </select>
                        <select id="year" class="rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                            ${this.generateYearOptions(currentYear)}
                        </select>
                    </div>
                </div>
            `;
        }

        createYearlySelector() {
            const currentYear = new Date().getFullYear();
            
            return `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Year</label>
                    <select id="year" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                        ${this.generateYearOptions(currentYear)}
                    </select>
                </div>
            `;
        }

        generateYearOptions(currentYear) {
            let yearsArray = [];
            for (let year = currentYear; year >= currentYear - 4; year--) {
                yearsArray.push(`<option value="${year}" ${year === currentYear ? 'selected' : ''}>${year}</option>`);
            }
            return yearsArray.join('');
        }

        getFilterParams() {
            const params = {
                report_type: this.reportType?.value || 'employee_activity',
                time_period: this.timePeriod?.value || 'monthly',
                year: document.getElementById('year')?.value || new Date().getFullYear()
            };

            // Bulan / quarter
            if (params.time_period === 'monthly') {
                const monthElement = document.getElementById('month');
                if (monthElement) params.month = monthElement.value;
            } else if (params.time_period === 'quarterly') {
                const quarterElement = document.getElementById('quarter');
                if (quarterElement) params.quarter = quarterElement.value;
            }

            console.log("Activity Filter Params:", params);
            return params;
        }

        init() {
            // Initialize the filter manager
            this.cacheElements();
            this.setupEventListeners();
            this.initializeDynamicPeriod();
            this.updatePeriodInputs();
            
            // Make this instance available globally for other scripts
            window.activityFilterManager = this;
            
            // Initialize with current page url path
            this.isBasRole = window.location.pathname.includes('/bas/');
            console.log(`[ActivityFilterManager] Initialized with prefix: ${this.isBasRole ? 'bas' : 'admin'}`);
        }
    }

    return {
        create: function() {
            return new ActivityFilterManager();
        }
    };
})();

// Inisialisasi ActivityFilterManager saat DOM siap
document.addEventListener('DOMContentLoaded', () => {
    window.activityFilterManager = ActivityFilterManagerApp.create();
});
