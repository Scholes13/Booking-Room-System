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
            this.filters = {}; // Menyimpan filter saat ini
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
            
            console.log('Filter elements initialized:', {
                reportType: this.reportType,
                timePeriod: this.timePeriod,
                periodContainer: this.periodContainer
            });
            
            // Periksa jika elemen tidak ditemukan
            if (!this.reportType) console.error('Element #report_type not found');
            if (!this.timePeriod) console.error('Element #time_period not found');
            if (!this.periodContainer) console.error('Element #period_container not found');
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
                year: document.getElementById('year')?.value || new Date().getFullYear().toString()
            };

            // Bulan / quarter
            if (params.time_period === 'monthly') {
                const monthElement = document.getElementById('month');
                if (monthElement) {
                    params.month = monthElement.value;
                    console.log('Month element value:', monthElement.value, 'type:', typeof monthElement.value);
                } else {
                    // Default to current month
                    params.month = (new Date().getMonth() + 1).toString();
                    console.log('Using default month:', params.month);
                }
            } else if (params.time_period === 'quarterly') {
                const quarterElement = document.getElementById('quarter');
                if (quarterElement) {
                    params.quarter = quarterElement.value;
                    console.log('Quarter element value:', quarterElement.value, 'type:', typeof quarterElement.value);
                } else {
                    // Default to current quarter
                    params.quarter = (Math.floor(new Date().getMonth() / 3) + 1).toString();
                    console.log('Using default quarter:', params.quarter);
                }
            }

            // Pastikan semua nilai numerik adalah string untuk konsistensi
            params.year = params.year.toString();
            if (params.month) params.month = params.month.toString();
            if (params.quarter) params.quarter = params.quarter.toString();

            // Simpan params sebagai properti filters
            this.filters = params;

            console.log("Activity Filter Params:", params);
            return params;
        }

        init() {
            // Initialize the filter manager
            this.initializeElements();
            this.initializeEventListeners();
            this.updatePeriodSelector();
            
            // Make this instance available globally for other scripts
            window.activityFilterManager = this;
            
            // Initialize with current page url path
            this.isBasRole = window.location.pathname.includes('/bas/');
            console.log(`[ActivityFilterManager] Initialized with prefix: ${this.isBasRole ? 'bas' : 'admin'}`);
            
            return this; // Return this for chaining
        }

        async fetchReportData() {
            const params = this.getFilterParams();
            let endpoint = 'data';
            
            // Gunakan endpoint /detailed untuk detailed_activity
            if (params.report_type === 'detailed_activity') {
                endpoint = 'detailed';
            }
            
            const url = `/${this.isBasRole ? 'bas' : 'admin'}/activity/${endpoint}`;
            
            console.log('Fetching report data:', {
                url: url,
                params: params,
                isBasRole: this.isBasRole
            });
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(params)
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Report data received:', data);
                return data;
            } catch (error) {
                console.error('Error fetching report data:', error);
                throw error;
            }
        }

        setReportGenerator(reportGenerator) {
            this.reportGenerator = reportGenerator;
        }
    }

    return {
        create: function() {
            return new ActivityFilterManager();
        }
    };
})();

// Inisialisasi ini tidak dibutuhkan lagi karena akan diinisialisasi di file init.js
// atau langsung di view

// document.addEventListener('DOMContentLoaded', () => {
//     window.activityFilterManager = ActivityFilterManagerApp.create();
// });
