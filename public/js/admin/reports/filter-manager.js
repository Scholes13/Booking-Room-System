const FilterManagerApp = (function() {
    class FilterManager {
        constructor(options = {}) {
            this.baseUrl = options.baseUrl || '/admin/reports';
            this.initializeElements();
            this.initializeEventListeners();
            this.updatePeriodSelector(); // Generate period selector on load
        }

        initializeElements() {
            this.timePeriod = document.getElementById('time_period');
            this.periodContainer = document.getElementById('period_container');
            this.reportType = document.getElementById('report_type');

            // Validate required elements
            if (!this.timePeriod || !this.periodContainer || !this.reportType) {
                console.error('Required elements for filter functionality are missing');
                return;
            }
        }

        initializeEventListeners() {
            if (!this.timePeriod) return;
            
            this.timePeriod.addEventListener('change', () => this.updatePeriodSelector());
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
            if (!this.reportType || !this.timePeriod) {
                console.error('Required filter elements are not initialized');
                return {};
            }

            const params = {
                report_type: this.reportType.value,
                time_period: this.timePeriod.value,
                year: document.getElementById('year')?.value || new Date().getFullYear()
            };

            switch (this.timePeriod.value) {
                case 'monthly':
                    const monthElement = document.getElementById('month');
                    if (monthElement) {
                        params.month = monthElement.value;
                    }
                    break;
                case 'quarterly':
                    const quarterElement = document.getElementById('quarter');
                    if (quarterElement) {
                        params.quarter = quarterElement.value;
                    }
                    break;
            }

            // Debug: Tampilkan filter parameters di console
            console.log("Filter Parameters:", params);
            return params;
        }
    }

    return {
        create: function(options = {}) {
            return new FilterManager(options);
        }
    };
})();

// Inisialisasi FilterManager saat DOM sudah siap
document.addEventListener('DOMContentLoaded', () => {
    // Determine the correct base URL based on the current path
    let baseUrl = '/admin/reports';
    if (window.location.pathname.includes('/bas/')) {
        baseUrl = '/bas/reports';
    } else if (window.location.pathname.includes('/superadmin/')) {
        baseUrl = '/superadmin/reports';
    }

    window.filterManager = FilterManagerApp.create({ baseUrl });
});
