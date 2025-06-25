@extends('admin.layout')

@section('title', 'Lead Worksheets')
@section('header', 'Lead Worksheets')

@push('styles')
<style>
    :root {
        --tbl-font-family: 'Inter', sans-serif;
        --tbl-font-size: 0.9rem;
        --tbl-header-background: #fff; /* No background color */
        --tbl-header-color: #555;
        --tbl-header-font-weight: 600;
        --tbl-header-font-size: 0.75rem;
        --tbl-header-text-transform: uppercase;
        --tbl-border-color: #e5e7eb;
        --tbl-row-background: #fff;
        --tbl-row-hover-background: #f9fafb;
        --tbl-cell-padding: 1rem 1.25rem; /* More padding: 16px top/bottom, 20px left/right */
        --tbl-action-button-background: #fef3c7; /* yellow-100 */
        --tbl-action-button-color: #92400e; /* amber-800 */
        --tbl-action-button-hover-background: #fde68a; /* yellow-200 */
    }

    /* Apply custom font */
    .tabulator {
        font-family: var(--tbl-font-family);
    }

    /* Main table styling */
    .tabulator {
        background-color: var(--tbl-row-background);
        border: 1px solid var(--tbl-border-color); /* A single, light outer border */
        border-radius: 8px; /* Rounded corners for the container */
        font-size: var(--tbl-font-size);
        overflow: hidden; /* Helps with border radius */
    }

    /* Header styling */
    .tabulator-header {
        background-color: var(--tbl-header-background);
        border-bottom: 2px solid var(--tbl-border-color);
        color: var(--tbl-header-color);
        font-weight: var(--tbl-header-font-weight);
    }
    .tabulator-col-title {
        text-transform: var(--tbl-header-text-transform);
        font-size: var(--tbl-header-font-size);
        letter-spacing: 0.05em;
        padding: 0.75rem 0; /* Adjust header padding */
    }
    
    /* Row styling */
    .tabulator-row {
        border-bottom: 1px solid var(--tbl-border-color);
        background-color: var(--tbl-row-background) !important;
        transition: background-color 0.2s ease-in-out;
    }
    .tabulator-row:last-of-type {
        border-bottom: none; /* No border for the last row */
    }
    .tabulator-row:hover {
        background-color: var(--tbl-row-hover-background) !important;
    }

    /* Cell styling */
    .tabulator-cell {
        padding: var(--tbl-cell-padding);
        border-right: none;
        line-height: 1.5;
        vertical-align: top; /* Align content to the top */
    }
    .tabulator-cell.tabulator-frozen {
        border-right: 2px solid var(--tbl-border-color);
    }
    
    /* Action Button Customization */
    .action-button-formatter a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem; /* 8px */
        border-radius: 6px;
        background-color: var(--tbl-action-button-background);
        color: var(--tbl-action-button-color);
        transition: background-color 0.2s ease;
    }
    .action-button-formatter a:hover {
        background-color: var(--tbl-action-button-hover-background);
        text-decoration: none;
    }
    .action-button-formatter .material-icons {
        font-size: 1.25rem; /* 20px */
    }

    /* Multi-line formatter content */
    .multi-line-formatter .title {
        font-weight: 600;
        color: #111827;
    }
    .multi-line-formatter .subtitle {
        font-size: 0.8rem;
        color: #6b7280;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 12px;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-badge.hot {
        background-color: var(--color-red-200);
        color: var(--color-red-800);
    }
    .status-badge.warm {
        background-color: var(--color-yellow-200);
        color: var(--color-yellow-800);
    }
    .status-badge.cold {
        background-color: var(--color-blue-200);
        color: var(--color-blue-800);
    }
    .status-badge.default {
        background-color: var(--color-gray-200);
        color: var(--color-gray-800);
    }

    /* Edit Button */
    .edit-button {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        background-color: var(--color-primary-light);
        color: var(--color-primary);
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.2s ease;
    }
    .edit-button:hover {
        background-color: var(--color-primary);
        color: white;
    }
    .edit-button .material-icons {
        font-size: 1rem;
    }

    /* Cell Content Styling */
    .cell-content-wrapper {
        position: relative;
        overflow: hidden;
        max-height: 6em; /* Approx 4 lines (1.5 line-height * 4) */
        /* transition: max-height 0.3s ease-in-out; -- This can interfere with height calculation */
    }
    .cell-content-wrapper.expanded {
        max-height: none; /* Let content define height */
    }
    .read-more-btn {
        color: var(--color-primary);
        font-weight: 600;
        cursor: pointer;
        display: block;
        margin-top: 5px;
        font-size: 0.8rem;
    }
    .read-more-btn:hover {
        text-decoration: underline;
    }

    /* Read More/Less Link */
    .read-more-link {
        color: var(--color-primary);
        font-weight: 600;
        font-size: 0.8rem;
        margin-top: 4px;
        display: inline-block;
        cursor: pointer;
    }
    .read-more-link:hover {
        text-decoration: underline;
    }

    /* Expandable Cell */
    .expandable-cell {
        word-break: break-word;
        white-space: pre-wrap !important;
    }
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Toolbar -->
    <div class="toolbar">
        <div class="toolbar-group">
            <button class="toolbar-button">
                <span class="material-icons">view_list</span>
                Views
                <span class="material-icons">arrow_drop_down</span>
            </button>
            <button class="toolbar-button"><span class="material-icons">sort</span> Sort</button>
            <button class="toolbar-button"><span class="material-icons">filter_list</span> Filter</button>
        </div>
        <div class="toolbar-group">
            
        </div>
    </div>

    <!-- Table -->
    <div id="lead-worksheet-table"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Custom Formatter for Expandable Text
    const expandableTextFormatter = function(cell, formatterParams, onRendered) {
        const text = cell.getValue();
        if (!text) return '';

        const cellEl = cell.getElement();
        cellEl.style.whiteSpace = "pre-wrap"; // Ensure wrapping

        // Create main content wrapper
        const wrapper = document.createElement('div');
        wrapper.classList.add('cell-content-wrapper');

        const content = document.createElement('div');
        content.textContent = text;
        wrapper.appendChild(content);

        // Check if content overflows
        onRendered(() => {
            // Only add "Read more" if the content is actually clamped
            if (content.scrollHeight > wrapper.clientHeight) {
                const button = document.createElement('a');
                button.textContent = 'Read more';
                button.classList.add('read-more-btn');
                
                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    wrapper.classList.toggle('expanded');
                    button.textContent = wrapper.classList.contains('expanded') ? 'Read less' : 'Read more';
                    // After changing content visibility, we MUST tell Tabulator to recalculate the row height.
                    cell.getRow().normalizeHeight();
                });
                
                // Append button outside the wrapper to keep it visible
                cellEl.appendChild(button);
            }
        });

        return wrapper;
    };

    // Custom Formatter for PIC Lead to show multi-line info
    const picLeadFormatter = function(cell, formatterParams, onRendered) {
        const rowData = cell.getRow().getData();
        if (!rowData.pic_lead) return '';

        // Main container
        const container = document.createElement('div');
        container.classList.add('multi-line-formatter');

        // PIC name
        const picName = document.createElement('div');
        picName.classList.add('title');
        picName.textContent = rowData.pic_lead;
        container.appendChild(picName);

        // Contact Person from survey as subtitle
        if (rowData.contact_person) {
            const contactPerson = document.createElement('div');
            contactPerson.classList.add('subtitle');
            contactPerson.textContent = `(Contact: ${rowData.contact_person})`;
            container.appendChild(contactPerson);
        }
        
        return container;
    };

    // Custom Formatter for Revenue
    const moneyFormatter = function(cell, formatterParams, onRendered) {
        const value = cell.getValue();
        if (value === null || value === undefined || value === '') {
            return '';
        }
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    }

    // Custom formatter for the edit button
    const editButtonFormatter = function(cell, formatterParams, onRendered) {
        const url = cell.getValue();
        if (!url) return '';

        const container = document.createElement('div');
        container.classList.add('action-button-formatter');

        const link = document.createElement('a');
        link.href = url;
        link.title = 'Edit';
        
        const icon = document.createElement('span');
        icon.classList.add('material-icons');
        icon.textContent = 'edit';
        
        link.appendChild(icon);
        container.appendChild(link);

        return container;
    };

    const table = new Tabulator("#lead-worksheet-table", {
        ajaxURL: "{{ route('lead.worksheets.data') }}",
        ajaxConfig: "GET",
        ajaxResponse: function(url, params, response) {
            return {
                data: response.data,
                last_page: response.last_page,
                current_page: response.current_page,
                total: response.total,
            };
        },
        pagination: true,
        paginationMode: "remote",
        paginationSize: 20,
        paginationSizeSelector: [10, 20, 50, 100],
        paginationInitialPage: 1,
        ajaxParams: {
            size: 20, // This should match paginationSize
        },
        layout: "fitColumns",
        resizableColumnFit: true,
        placeholder: "No Lead Worksheets Found",
        columns: [
            { title: "ID", field: "id", visible: false },
            { title: "Company", field: "company_name", minWidth: 200, frozen: true },
            { title: "Project Name", field: "project_name", minWidth: 180 },
            { title: "Service Type", field: "service_type", minWidth: 150 },
            { title: "Line of Business", field: "line_of_business", minWidth: 150 },
            { title: "Status of Lead", field: "status_of_lead", minWidth: 130 },
            { title: "PIC Lead", field: "pic_lead", minWidth: 150 },
            { title: "Follow Up Status", field: "follow_up_status", minWidth: 150 },
            { 
                title: "Latest Note", 
                field: "follow_up_note", 
                minWidth: 400,
                formatter: expandableTextFormatter
            },
            { 
                title: "Requirements", 
                field: "requirements", 
                minWidth: 400,
                formatter: expandableTextFormatter
            },
            { title: "Est. Revenue", field: "estimated_revenue", minWidth: 150, hozAlign: "right", formatter: moneyFormatter },
            { title: "Mat. Revenue", field: "materialized_revenue", minWidth: 150, hozAlign: "right", formatter: moneyFormatter },
            { title: "Contact Person", field: "contact_person", minWidth: 150 },
            { title: "Contact Phone", field: "contact_phone", minWidth: 150 },
            { 
                title: "Action", 
                field: "edit_url", 
                width: 120, 
                hozAlign: "center", 
                formatter: editButtonFormatter,
                cellClick: function(e, cell) {
                    e.stopPropagation();
                }
            }
        ]
    });

    // Handle pagination info from headers
    table.on("tableBuilt", function(){
        table.on("ajaxResponse", function(url, params, response){
            const headers = this.modules.ajax.getLastRequestResponse().getResponseHeaders();
            const lastPage = parseInt(headers['x-pagination-last-page']);
            if (lastPage) {
                this.setPageSize(20); // Reset page size to match backend
                this.setMaxPage(lastPage);
            }
        });
    });
});
</script>
@endpush 