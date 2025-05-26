<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Visits Schedule</title>
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --accent-color: #f59e0b;
            --accent-dark: #d97706;
            --text-light: #f3f4f6;
            --text-dark: #1f2937;
            --bg-dark: #0f172a;
            --bg-darker: #0a0f1d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--bg-darker) 0%, var(--bg-dark) 100%);
            color: var(--text-light);
            min-height: 100vh;
            line-height: 1.5;
        }
        
        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: white;
        }
        
        /* View Toggle */
        .view-controls {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 0.5rem;
            max-width: fit-content;
        }
        
        .view-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .view-btn.active {
            background-color: rgba(255, 255, 255, 0.9);
            color: var(--bg-dark);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        /* Filter Section */
        .filter-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        @media (min-width: 768px) {
            .filter-form {
                grid-template-columns: repeat(3, 1fr);
                gap: 1.5rem;
            }
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            position: relative;
        }
        
        .filter-label {
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
        }
        
        /* Custom Select Styling */
        .custom-select-wrapper {
            position: relative;
        }
        
        .custom-select {
            appearance: none;
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            padding-right: 2.5rem;
            font-size: 1rem;
            color: white;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .custom-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }
        
        .custom-select-arrow {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Override select styling for better readability */
        select.custom-select option {
            background-color: #fff;
            color: #333;
            padding: 10px;
            font-size: 16px;
        }
        
        /* Date input styling */
        .filter-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            font-size: 1rem;
            transition: all 0.2s;
        }
        
        .filter-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }
        
        .filter-btn-group {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-secondary {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
        }
        
        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Results Counter */
        .results-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            padding: 0.5rem;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-amber {
            background-color: var(--accent-color);
            color: white;
        }
        
        /* Table Styles */
        .table-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
        }
        
        .table-overflow {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        thead {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        th {
            text-align: left;
            padding: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.7);
            white-space: nowrap;
        }
        
        td {
            padding: 1rem;
            vertical-align: top;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }
        
        .cell-team,
        .cell-company,
        .cell-main {
            font-weight: 600;
            color: white;
        }
        
        .cell-sub {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 0.25rem;
        }
        
        .btn-view {
            padding: 0.5rem 0.75rem;
            background-color: rgba(59, 130, 246, 0.2);
            color: rgb(147, 197, 253);
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        
        .btn-view:hover {
            background-color: rgba(59, 130, 246, 0.3);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .status-scheduled {
            background-color: rgba(59, 130, 246, 0.2);
            color: rgb(147, 197, 253);
        }
        
        .status-ongoing {
            background-color: rgba(16, 185, 129, 0.2);
            color: rgb(110, 231, 183);
        }
        
        .status-completed {
            background-color: rgba(139, 92, 246, 0.2);
            color: rgb(192, 132, 252);
        }
        
        .status-cancelled {
            background-color: rgba(239, 68, 68, 0.2);
            color: rgb(252, 165, 165);
        }
        
        /* Mobile Optimizations */
        @media (max-width: 640px) {
            .filter-btn-group {
                flex-direction: column;
            }
            
            th, td {
                padding: 0.75rem;
            }
            
            .fc .fc-toolbar.fc-header-toolbar {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .fc .fc-toolbar-title {
                font-size: 1rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            /* Table to Card conversion */
            .table-overflow {
                display: none;
            }
            
            .mobile-cards {
                display: flex;
            }
            
            .pagination-wrapper {
                border-top: none;
            }
            
            .card-info-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .card-info-item {
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 0.75rem;
                padding-bottom: 0.5rem;
                border-bottom: none !important;
            }
            
            .card-info-label {
                margin-bottom: 0;
                width: 32px;
                height: 32px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            
            .card-info-label i {
                margin: 0;
            }
            
            .card-info-value {
                display: flex;
                flex-direction: column;
            }
            
            .btn-view {
                width: 100%;
                justify-content: center;
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
                background-color: rgba(59, 130, 246, 0.3);
                transition: all 0.2s ease;
            }
            
            .btn-view:hover, .btn-view:active {
                background-color: rgba(59, 130, 246, 0.5);
            }
            
            .card-footer {
                flex-direction: column;
                gap: 1rem;
            }
            
            .card-actions {
                width: 100%;
            }
        }
        
        /* Mobile Card View */
        .mobile-cards {
            display: none;
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
        }
        
        .visit-card {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .visit-card:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--primary-color);
            opacity: 0.7;
        }
        
        .visit-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .card-team {
            font-weight: 700;
            font-size: 1.125rem;
            color: white;
            padding-left: 0.5rem;
        }
        
        .card-company {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: white;
            padding-left: 0.5rem;
        }
        
        .card-pic {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 1.25rem;
            padding-left: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-pic i {
            color: var(--accent-color);
            font-size: 0.75rem;
        }
        
        .card-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
            background: rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            padding: 1rem;
        }
        
        .card-info-item {
            display: flex;
            flex-direction: column;
        }
        
        .card-info-label {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        
        .card-info-label i {
            color: var(--accent-color);
        }
        
        .card-info-value {
            font-size: 0.875rem;
            color: white;
            font-weight: 500;
        }
        
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .card-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-overflow {
                display: none;
            }
            
            .mobile-cards {
                display: flex;
            }
            
            .pagination-wrapper {
                border-top: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="page-title">Field Visits Schedule</h1>
        
        <!-- Filter Section -->
        <div class="filter-card">
            <form id="filter-form" action="{{ route('public.field-visits.index') }}" method="GET" class="filter-form">
                <!-- Team Filter -->
                <div class="filter-group">
                    <label for="team_id" class="filter-label">
                        <i class="fas fa-users"></i>
                        Team
                    </label>
                    <div class="custom-select-wrapper">
                        <select id="team_id" name="team_id" class="custom-select">
                            <option value="">All Teams</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="custom-select-arrow">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <!-- Location Filter -->
                <div class="filter-group">
                    <label for="location" class="filter-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Location
                    </label>
                    <div class="custom-select-wrapper">
                        <select id="location" name="location" class="custom-select">
                            <option value="">All Locations</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('location') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                        <div class="custom-select-arrow">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="filter-group">
                    <label for="date" class="filter-label"><i class="fas fa-calendar-alt"></i> Date</label>
                    <input type="date" name="date" id="date" class="filter-input"
                           value="{{ request('date', $filterDate ?? '') }}">
                </div>

                <div class="filter-group" style="align-self: end; justify-content: flex-start;"> 
                    @if(request()->hasAny(['team_id', 'location', 'date']))
                        <a href="{{ route('public.field-visits.index') }}" class="btn btn-secondary" style="width: auto; padding-left: 1rem; padding-right: 1rem;">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Results Counter -->
        <div class="results-info">
            <i class="fas fa-info-circle"></i>
            <span>Showing {{ $assignments->firstItem() ?? 0 }} - {{ $assignments->lastItem() ?? 0 }} of {{ $assignments->total() }} field visits</span>
            @if(request()->hasAny(['team_id', 'location', 'date']))
                <span class="badge badge-amber">Filtered</span>
            @endif
        </div>
        
        <!-- Table View -->
        <div id="table-view" class="table-card">
            <div class="table-overflow">
                <table>
                    <thead>
                        <tr>
                            <th>Team</th>
                            <th>Company</th>
                            <th>Location</th>
                            <th>Date</th>
                            <th class="text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td>
                                <div class="cell-team">{{ $assignment->team->name }}</div>
                            </td>
                            <td>
                                <div class="cell-company">{{ $assignment->activity->salesMissionDetail->company_name ?? 'N/A' }}</div>
                                <div class="cell-sub">
                                    {{ $assignment->activity->salesMissionDetail->company_pic ?? '' }} 
                                    @if($assignment->activity->salesMissionDetail->company_position ?? false)
                                        ({{ $assignment->activity->salesMissionDetail->company_position }})
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="cell-main">{{ $assignment->activity->city }}</div>
                                <div class="cell-sub">{{ $assignment->activity->province }}</div>
                            </td>
                            <td>
                                <div class="cell-main">{{ $assignment->activity->start_datetime ? \Carbon\Carbon::parse($assignment->activity->start_datetime)->format('d M Y') : 'N/A' }}</div>
                                <div class="cell-sub">{{ $assignment->activity->start_datetime ? \Carbon\Carbon::parse($assignment->activity->start_datetime)->format('H:i') : '' }}</div>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('public.field-visits.detail', $assignment) }}" class="btn-view">
                                    <i class="fas fa-eye"></i>
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times empty-icon"></i>
                                    <h3 class="empty-title">No Field Visits Found</h3>
                                    <p class="empty-message">Try adjusting your filters to see more results</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards View -->
            <div class="mobile-cards">
                @forelse($assignments as $assignment)
                <div class="visit-card">
                    <div class="card-header">
                        <div class="card-team">{{ $assignment->team->name }}</div>
                    </div>
                    
                    <div class="card-company">{{ $assignment->activity->salesMissionDetail->company_name ?? 'N/A' }}</div>
                    
                    <div class="card-pic">
                        <i class="fas fa-user-tie"></i>
                        <div>
                            {{ $assignment->activity->salesMissionDetail->company_pic ?? 'N/A' }} 
                            @if($assignment->activity->salesMissionDetail->company_position ?? false)
                                <span>({{ $assignment->activity->salesMissionDetail->company_position }})</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-info-grid">
                        <div class="card-info-item">
                            <div class="card-info-label">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="card-info-value">
                                {{ $assignment->activity->city }}
                                <div class="cell-sub">{{ $assignment->activity->province }}</div>
                            </div>
                        </div>
                        
                        <div class="card-info-item">
                            <div class="card-info-label">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="card-info-value">
                                {{ $assignment->activity->start_datetime ? \Carbon\Carbon::parse($assignment->activity->start_datetime)->format('d M Y') : 'N/A' }}
                                <div class="cell-sub">{{ $assignment->activity->start_datetime ? \Carbon\Carbon::parse($assignment->activity->start_datetime)->format('H:i') : '' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="card-actions">
                            <a href="{{ route('public.field-visits.detail', $assignment) }}" class="btn-view">
                                <i class="fas fa-eye"></i>
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-calendar-times empty-icon"></i>
                    <h3 class="empty-title">No Field Visits Found</h3>
                    <p class="empty-message">Try adjusting your filters to see more results</p>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /* // View toggle functionality -- DIKOMENTARI KARENA TOMBOL SUDAH TIDAK ADA
            const tableViewBtn = document.getElementById('table-view-btn');
            const calendarViewBtn = document.getElementById('calendar-view-btn');
            const tableView = document.getElementById('table-view');
            const calendarView = document.getElementById('calendar-view');
            
            if (tableViewBtn && calendarViewBtn && tableView && calendarView) { // Check if elements exist
                tableViewBtn.addEventListener('click', function() {
                    tableView.style.display = 'block';
                    calendarView.style.display = 'none';
                    tableViewBtn.classList.add('active');
                    calendarViewBtn.classList.remove('active');
                    localStorage.setItem('fieldVisitsView', 'table');
                });
                
                calendarViewBtn.addEventListener('click', function() {
                    tableView.style.display = 'none';
                    calendarView.style.display = 'block';
                    calendarViewBtn.classList.add('active');
                    tableViewBtn.classList.remove('active');
                    localStorage.setItem('fieldVisitsView', 'calendar');
                    if (calendar) calendar.updateSize(); // Pastikan calendar dirender ulang
                });
            }
            */
            
            // Filter functionality - auto submit on change
            const filterForm = document.getElementById('filter-form');
            const teamFilter = document.getElementById('team_id');
            const locationFilter = document.getElementById('location');
            const dateFilter = document.getElementById('date');
            
            if (filterForm && teamFilter && locationFilter && dateFilter) { // Pastikan semua elemen filter ada
                const enhanceDropdowns = () => {
                    const selects = document.querySelectorAll('.custom-select');
                    selects.forEach(select => {
                        select.addEventListener('touchstart', function() {
                            this.classList.add('touched');
                        });
                        
                        select.addEventListener('change', function() {
                            filterForm.submit();
                        });
                    });
                };
                
                enhanceDropdowns();
                
                dateFilter.addEventListener('change', function() {
                    filterForm.submit();
                });
                
                const resetFilterLink = document.querySelector('a[href="{{ route("public.field-visits.index") }}"]');
                if (resetFilterLink) {
                    resetFilterLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        teamFilter.value = '';
                        locationFilter.value = '';
                        dateFilter.value = '';
                        filterForm.submit();
                    });
                }
            } // End of filter functionality check
            
            /* // Calendar initialization -- DIHAPUS
            // ... (kode inisialisasi kalender)
            */
            
            /* // Event Modal functionality -- DIHAPUS
            // ... (kode modal event)
            */
            
            /* // Check saved preference -- DIKOMENTARI KARENA VIEW TOGGLE SUDAH TIDAK ADA
            // ... (kode saved preference dikomentari sebelumnya)
            */
            
            /* // Add swipe gesture support for mobile -- DIKOMENTARI KARENA VIEW TOGGLE SUDAH TIDAK ADA
            // ... (kode swipe gesture dikomentari sebelumnya)
            */
        });
    </script>
</body>
</html> 