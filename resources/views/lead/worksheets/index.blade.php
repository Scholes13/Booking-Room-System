@extends('admin.layout')

@section('title', 'Lead Worksheets')
@section('header', 'Lead Worksheets')

@push('styles')
<style>
    /* Adopt Inter font for this page */
    .tabulator {
        font-family: 'Inter', sans-serif;
    }

    /* Override Tabulator's theme to match the reference design */
    .tabulator {
        background-color: #fff;
        border: none;
        font-size: 0.9rem;
        overflow: visible; /* Allow shadows from toolbar to show */
    }
    .tabulator-header {
        background-color: #fafafa;
        border-bottom: 2px solid #ddd;
        color: #444;
        font-weight: 600;
        padding: 4px 0;
    }
    .tabulator-col-title {
        text-transform: none;
        font-size: 0.85rem;
        color: #444;
    }
    .tabulator-row {
        border-bottom: 1px solid #ededed;
        transition: background-color 0.2s ease-in-out;
    }
    .tabulator-row:hover {
        background-color: #f3e7c9; /* Primary light color from ref */
    }
    .tabulator-cell {
        padding: 12px 16px;
        border-right: none;
    }
    .tabulator-cell.tabulator-frozen {
        border-right: 2px solid #ddd;
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
    // Helper to measure text width
    function measureTextWidth(text, font) {
        const canvas = measureTextWidth.canvas || (measureTextWidth.canvas = document.createElement("canvas"));
        const context = canvas.getContext("2d");
        context.font = font || '14px Inter, sans-serif';

        const lines = text.split('\n');
        let maxWidth = 0;
        lines.forEach(line => {
            const metrics = context.measureText(line);
            if (metrics.width > maxWidth) {
                maxWidth = metrics.width;
            }
        });
        return maxWidth + 40; 
    }

    // Global state for our dynamic columns
    const dynamicColumnState = {
        requirements: { minWidth: 250, maxWidth: 700, expanded: {} }, // { rowId: { width } }
        follow_up_note: { minWidth: 250, maxWidth: 700, expanded: {} }
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

    // The Final, Definitive "Read More" Formatter
    const createDynamicReadMoreFormatter = (columnField) => {
        return function(cell, formatterParams, onRendered) {
            const text = cell.getValue();
            if (!text) return '';

            const row = cell.getRow();
            const rowId = row.getPosition();
            const columnState = dynamicColumnState[columnField];
            const isExpanded = !!columnState.expanded[rowId];
            
            const wrapper = document.createElement('div');
            wrapper.classList.add('expandable-cell');
            const content = document.createElement('div');
            content.textContent = text;

            const isLongText = (text.length > 80 || (text.match(/\n/g) || []).length + 1 > 1);

            if (isLongText && !isExpanded) {
                content.classList.add('line-clamp-1');
            }
            wrapper.appendChild(content);

            if (isLongText) {
                const button = document.createElement('a');
                button.href = '#';
                button.classList.add('read-more-link');
                button.textContent = isExpanded ? 'Read Less' : 'Read More';

                button.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const table = cell.getTable();
                    const column = cell.getColumn();
                    const tableHolder = table.element.querySelector('.tabulator-tableholder');
                    const scrollLeft = tableHolder ? tableHolder.scrollLeft : 0;

                    if (isExpanded) {
                        delete columnState.expanded[rowId];
                    } else {
                        const measuredWidth = measureTextWidth(text);
                        columnState.expanded[rowId] = { width: Math.max(measuredWidth, columnState.minWidth) };
                    }

                    const allExpanded = Object.values(columnState.expanded);
                    const requiredWidth = allExpanded.length > 0 ? Math.max(...allExpanded.map(e => e.width)) : columnState.minWidth;
                    const newWidth = Math.min(requiredWidth, columnState.maxWidth);
                    
                    column.setWidth(newWidth).then(() => {
                        // After width is set, reformat the row to apply the clamp/unclamp logic
                        row.reformat();
                        // Then, normalize the height to fit the new content
                        row.normalizeHeight();
                        // Finally, restore scroll position
                        const newTableHolder = table.element.querySelector('.tabulator-tableholder');
                        if (newTableHolder) {
                            newTableHolder.scrollLeft = scrollLeft;
                        }
                    });
                };
                wrapper.appendChild(button);
            }
            return wrapper;
        };
    };

    // Status Badge Formatter
    const statusBadgeFormatter = function(cell, formatterParams, onRendered) {
        const status = cell.getValue()?.toLowerCase() || '';
        let colorClass = 'default';
        
        if (status === 'hot') colorClass = 'hot';
        else if (status === 'warm') colorClass = 'warm';
        else if (status === 'cold') colorClass = 'cold';
        
        return `<span class="status-badge ${colorClass}">${cell.getValue() || 'N/A'}</span>`;
    };

    // Edit Button Formatter
    const editButtonFormatter = function(cell, formatterParams, onRendered) {
        const url = cell.getValue();
        return `<a href="${url}" class="edit-button"><span class="material-icons">edit</span>Edit</a>`;
    };

    try {
        const table = new Tabulator("#lead-worksheet-table", {
            data: @json($worksheets),
            layout: "fitDataStretch",
            pagination: "local",
            paginationSize: 15,
            paginationSizeSelector: [10, 15, 20, 50, 100],
            columns: [
                { 
                    title: "Company",
                    field: "company_name",
                    minWidth: 200,
                    headerSort: true
                },
                { 
                    title: "Project Name",
                    field: "project_name",
                    minWidth: 180,
                    headerSort: true
                },
                { 
                    title: "Jenis Layanan",
                    field: "service_type",
                    minWidth: 200,
                    headerSort: true
                },
                { 
                    title: "Business Purpose",
                    field: "line_of_business",
                    minWidth: 180,
                    headerSort: true
                },
                { 
                    title: "Status of Lead",
                    field: "status_of_lead",
                    minWidth: 150,
                    hozAlign: "center",
                    headerSort: true,
                    formatter: statusBadgeFormatter
                },
                { 
                    title: "PIC Lead",
                    field: "pic_lead",
                    minWidth: 150,
                    headerSort: true
                },
                { 
                    title: "FollowUp Lead",
                    field: "follow_up_status",
                    minWidth: 130,
                    headerSort: true
                },
                { 
                    title: "Note Follow Up Lead",
                    field: "follow_up_note",
                    width: 250,
                    headerSort: false,
                    formatter: createDynamicReadMoreFormatter('follow_up_note')
                },
                { 
                    title: "Requirements Lead",
                    field: "requirements",
                    width: 250,
                    headerSort: false,
                    formatter: createDynamicReadMoreFormatter('requirements')
                },
                { 
                    title: "Estimated Revenue",
                    field: "estimated_revenue",
                    minWidth: 170,
                    hozAlign: "right",
                    headerSort: true,
                    formatter: moneyFormatter
                },
                { 
                    title: "Materialized",
                    field: "materialized_revenue",
                    minWidth: 170,
                    hozAlign: "right",
                    headerSort: true,
                    formatter: moneyFormatter
                },
                { 
                    title: "Contact",
                    field: "contact_person",
                    minWidth: 180,
                    headerSort: true
                },
                { 
                    title: "Phone Number",
                    field: "contact_phone",
                    minWidth: 150,
                    headerSort: true
                },
                {
                    title: "Action",
                    field: "edit_url",
                    formatter: editButtonFormatter,
                    width: 100,
                    hozAlign: "center",
                    headerSort: false,
                }
            ],
            rowHeight: 60, // Set a fixed row height
            initialSort: [
                {column: "company_name", dir: "asc"}
            ]
        });

        // Handle any errors during initialization
        table.on("tableBuilt", function(){
            console.log("Table built successfully");
        });

        table.on("error", function(error){
            console.error("Tabulator Error:", error);
        });

    } catch (error) {
        console.error("Error initializing table:", error);
    }
});
</script>
@endpush 