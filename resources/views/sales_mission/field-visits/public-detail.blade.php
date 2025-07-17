<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Visit Details - {{ $fieldVisit->activity->salesMissionDetail->company_name ?? 'Details' }}</title>
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
            margin-bottom: 1.5rem;
            color: white;
        }
        
        /* Back link */
        .back-link {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 0;
            color: #60a5fa;
            font-size: 0.95rem;
            text-decoration: none;
            transition: color 0.2s;
            margin-bottom: 2rem;
        }
        
        .back-link:hover {
            color: #93c5fd;
        }
        
        .back-link i {
            margin-right: 0.5rem;
        }
        
        /* Header Card */
        .header-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        @media (min-width: 768px) {
            .header-card {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        
        .company-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }
        
        .info-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .info-group i {
            width: 1.25rem;
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: capitalize;
            align-self: flex-start;
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
        
        /* Card Layout */
        .card-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        @media (min-width: 768px) {
            .card-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
        }
        
        .full-width-card {
            grid-column: 1 / -1;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: white;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .data-section {
            margin-bottom: 1.5rem;
        }
        
        .data-section:last-child {
            margin-bottom: 0;
        }
        
        .data-label {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0.25rem;
        }
        
        .data-value {
            font-size: 1rem;
            color: white;
            font-weight: 500;
        }
        
        .data-subtext {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 0.25rem;
        }
        
        /* Team members */
        .team-members {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .member-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .member-avatar {
            width: 2.5rem;
            height: 2.5rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }
        
        .member-info {
            display: flex;
            flex-direction: column;
        }
        
        .member-name {
            font-weight: 600;
            color: white;
        }
        
        .member-position {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Activity details grid */
        .activity-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        @media (min-width: 768px) {
            .activity-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        /* Footer buttons */
        .actions-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }
        
        .btn-outline {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }
        
        .btn-primary {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--accent-dark);
        }
        
        .btn i {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="page-title">Werkudara OpsCenter</h1>
        
        @php
            // Prepare the back link URL with all existing query parameters from the previous request
            // This ensures that when the user clicks "Back to list", they return to the exact
            // filtered and paginated view they were on.
            $backUrl = route('public.field-visits.index');
            $queryParams = request()->query(); // Gets all current query params (passed from index to detail)
            
            // If there are query parameters, append them to the back URL
            if (!empty($queryParams)) {
                $backUrl .= '?' . http_build_query($queryParams);
            }
        @endphp
        <a href="{{ $backUrl }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to List
        </a>
        
        <!-- Header -->
        <div class="header-card">
            <div>
                <h1 class="company-name">{{ $fieldVisit->activity->salesMissionDetail->company_name ?? 'No Company Name' }}</h1>
                <div class="info-group">
                    <i class="fas fa-users"></i>
                    <span>{{ $fieldVisit->team->name }}</span>
                </div>
                <div class="info-group">
                    <i class="fas fa-calendar"></i>
                    <span>
                        {{ $fieldVisit->activity->start_datetime ? \Carbon\Carbon::parse($fieldVisit->activity->start_datetime)->format('d M Y') : 'N/A' }}
                        •
                        {{ $fieldVisit->activity->start_datetime ? \Carbon\Carbon::parse($fieldVisit->activity->start_datetime)->format('H:i') : '' }}
                        @if($fieldVisit->activity->end_datetime)
                            - {{ \Carbon\Carbon::parse($fieldVisit->activity->end_datetime)->format('H:i') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card-grid">
            <!-- Company Information -->
            <div class="info-card">
                <h2 class="card-title">Company Information</h2>
                
                <div class="data-section">
                    <div class="data-label">Company Name</div>
                    <div class="data-value">{{ $fieldVisit->activity->salesMissionDetail->company_name ?? 'N/A' }}</div>
                </div>
                
                <div class="data-section">
                    <div class="data-label">Contact Person</div>
                    <div class="data-value">{{ $fieldVisit->activity->salesMissionDetail->company_pic ?? 'N/A' }}</div>
                    @if($fieldVisit->activity->salesMissionDetail->company_position ?? false)
                        <div class="data-subtext">{{ $fieldVisit->activity->salesMissionDetail->company_position }}</div>
                    @endif
                    @if($fieldVisit->activity->salesMissionDetail->company_contact ?? false)
                        <div class="data-subtext"><i class="fas fa-phone-alt"></i> {{ $fieldVisit->activity->salesMissionDetail->company_contact }}</div>
                    @endif
                </div>
                
                <div class="data-section">
                    <div class="data-label">Address</div>
                    <div class="data-value">{{ $fieldVisit->activity->salesMissionDetail->company_address ?? 'N/A' }}</div>
                    <div class="data-subtext">
                        {{ $fieldVisit->activity->city ?? '' }}
                        @if($fieldVisit->activity->province)
                            , {{ $fieldVisit->activity->province }}
                        @endif
                    </div>
                </div>
                
                @if($fieldVisit->activity->salesMissionDetail->notes ?? false)
                <div class="data-section">
                    <div class="data-label">Notes</div>
                    <div class="data-value">{{ $fieldVisit->activity->salesMissionDetail->notes }}</div>
                </div>
                @endif
            </div>
            
            <!-- Team Information -->
            <div class="info-card">
                <h2 class="card-title">Team Information</h2>
                
                <div class="data-section">
                    <div class="data-label">Team Name</div>
                    <div class="data-value">{{ $fieldVisit->team->name }}</div>
                </div>
                
                <div class="data-section">
                    <div class="data-label">Team Members</div>
                    <div class="team-members">
                        @forelse($fieldVisit->team->members as $member)
                        <div class="member-item">
                            <div class="member-avatar">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <div class="member-info">
                                <span class="member-name">{{ $member->name }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="data-value">No team members listed.</p>
                        @endforelse
                    </div>
                </div>
                
                @if($fieldVisit->notes)
                <div class="data-section">
                    <div class="data-label">Assignment Notes</div>
                    <div class="data-value">{{ $fieldVisit->notes }}</div>
                </div>
                @endif
            </div>
            
            <!-- Activity Details -->
            <div class="info-card full-width-card">
                <h2 class="card-title">Activity Details</h2>
                
                <div class="activity-grid">
                    <div class="data-section">
                        <div class="data-label">Activity Type</div>
                        <div class="data-value">{{ $fieldVisit->activity->activity_type ?? 'Field Visit' }}</div>
                    </div>
                    
                    <div class="data-section">
                        <div class="data-label">Start Date & Time</div>
                        <div class="data-value">
                            {{ $fieldVisit->activity->start_datetime ? \Carbon\Carbon::parse($fieldVisit->activity->start_datetime)->format('d M Y H:i') : 'N/A' }}
                        </div>
                    </div>
                    
                    <div class="data-section">
                        <div class="data-label">End Date & Time</div>
                        <div class="data-value">
                            {{ $fieldVisit->activity->end_datetime ? \Carbon\Carbon::parse($fieldVisit->activity->end_datetime)->format('d M Y H:i') : 'N/A' }}
                        </div>
                    </div>
                </div>
                
                @if(!empty($fieldVisit->activity->description))
                <div class="data-section" style="margin-top: 1.5rem;">
                    <div class="data-label">Description</div>
                    <div class="data-value">{{ $fieldVisit->activity->description }}</div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="actions-footer">
            <a href="{{ $backUrl }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
            @if(isset($fieldVisit->feedbackSurvey) && $fieldVisit->feedbackSurvey->survey_token)
                @php
                    $isCompleted = $fieldVisit->feedbackSurvey->is_completed;
                    $reportButtonText = $isCompleted ? 'View Report' : 'Submit Report';
                    $reportButtonIcon = $isCompleted ? 'fa-check-circle' : 'fa-clipboard-list'; // Icon asli jika belum, atau centang jika sudah
                    $reportButtonStyle = $isCompleted 
                        ? 'background-color: rgba(16, 185, 129, 0.2); color: rgb(110, 231, 183);' // Greenish
                        : 'background-color: var(--accent-color);'; // Default accent color from CSS variables
                    // Untuk tombol utama di detail, kita mungkin ingin warna teksnya tetap putih jika defaultnya adalah accent-color
                    if (!$isCompleted) {
                         $reportButtonStyle .= ' color: white;';
                    }
                @endphp
                <a href="{{ $fieldVisit->feedbackSurvey->public_url }}"
                   target="_blank" 
                   class="btn btn-primary" 
                   style="{{ $reportButtonStyle }}">
                    <i class="fas {{ $reportButtonIcon }}"></i>
                    {{ $reportButtonText }}
                </a>
            @endif
        </div>
    </div>
</body>
</html> 