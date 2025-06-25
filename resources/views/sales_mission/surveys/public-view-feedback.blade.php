<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        ::-webkit-scrollbar {
            width: 8px; height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #e5e7eb; /* Tailwind gray-200 for track */
        }
        ::-webkit-scrollbar-thumb {
            background: #9ca3af; /* Tailwind gray-400 for thumb */
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280; /* Tailwind gray-500 for thumb hover */
        }
        .detail-item strong {
            font-weight: 500; /* medium */
            text-transform: uppercase;
            letter-spacing: 0.05em; /* tracking-wider */
            color: #6b7280; /* text-gray-500 */
            font-size: 0.75rem; /* text-xs */
            display: block;
            margin-bottom: 0.25rem; /* mb-1 */
        }
        .detail-item span {
            color: #1f2937; /* text-gray-800 */
            font-size: 0.875rem; /* text-sm */
            display: block;
            padding: 0.5rem 0.75rem; /* py-2 px-3 */
            background-color: #f9fafb; /* bg-gray-50 */
            border: 1px solid #e5e7eb; /* border-gray-200 */
            border-radius: 0.375rem; /* rounded-md */
            min-height: 38px; /* Ensure consistent height like inputs */
        }
        .detail-item span.checkbox-value {
            padding: 0;
            background-color: transparent;
            border: none;
            min-height: auto;
        }
        .detail-item span.checkbox-value .status {
            font-weight: 500;
        }
        .detail-item span.checkbox-value .text-green-600 {
            color: #059669;
        }
        .detail-item span.checkbox-value .text-red-600 {
            color: #dc2626;
        }
    </style>
</head>
<body class="bg-slate-100 text-gray-800 text-sm">
    <div class="max-w-4xl mx-auto py-8 sm:py-12 px-4 sm:px-6 lg:px-8">

        <div class="bg-indigo-600 p-6 sm:p-8 rounded-t-xl shadow-lg">
            <h1 class="text-2xl sm:text-3xl font-bold text-white text-center">Feedback Details</h1>
        </div>

        <div class="bg-white shadow-xl rounded-b-xl p-6 sm:p-8">
            @if ($survey)
                <!-- Section for Field Visit or Sales Blitz Info -->
                @if ($survey->survey_type === 'field_visit' && $survey->teamAssignment)
                    <div class="border border-slate-200 rounded-lg p-5 mb-8 shadow-sm">
                        <h2 class="text-base font-semibold text-indigo-700 px-2 py-1 bg-slate-50 rounded-md tracking-wide inline-block mb-4">Team Visit Information</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                            <div class="detail-item"><strong>Company:</strong> <span>{{ optional(optional($survey->teamAssignment)->activity)->salesMissionDetail->company_name ?? 'N/A' }}</span></div>
                            <div class="detail-item"><strong>Team:</strong> <span>{{ optional(optional($survey->teamAssignment)->team)->name ?? 'N/A' }}</span></div>
                            <div class="detail-item"><strong>Date of Visit:</strong>
                                <span>
                                    @php
                                        $activityForDate = optional($survey->teamAssignment)->activity;
                                        $visit_start_datetime = $activityForDate ? $activityForDate->start_datetime : null;
                                    @endphp
                                    {{ $visit_start_datetime ? \Carbon\Carbon::parse($visit_start_datetime)->format('d M Y H:i') : 'N/A' }}
                                </span>
                            </div>
                            <div class="detail-item"><strong>Location:</strong> <span>{{ optional(optional($survey->teamAssignment)->activity)->city ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                @elseif ($survey->survey_type === 'sales_blitz')
                    <div class="border border-slate-200 rounded-lg p-5 mb-8 shadow-sm">
                        <h2 class="text-base font-semibold text-indigo-700 px-2 py-1 bg-slate-50 rounded-md tracking-wide inline-block mb-4">Sales Blitz Information</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                            <div class="detail-item"><strong>Team Name / Sales Person:</strong> <span>{{ $survey->blitzTeam->name ?? ($survey->blitz_team_name ?? 'N/A') }}</span></div>
                            <div class="detail-item"><strong>Company Visited:</strong> <span>{{ $survey->blitz_company_name ?? 'N/A' }}</span></div>
                            <div class="detail-item"><strong>Visit Start Time:</strong> <span>{{ $survey->blitz_visit_start_datetime ? \Carbon\Carbon::parse($survey->blitz_visit_start_datetime)->format('D, d M Y, H:i') : 'N/A' }}</span></div>
                            <div class="detail-item"><strong>Visit End Time:</strong> <span>{{ $survey->blitz_visit_end_datetime ? \Carbon\Carbon::parse($survey->blitz_visit_end_datetime)->format('D, d M Y, H:i') : 'N/A' }}</span></div>
                        </div>
                    </div>
                @endif

                <!-- Contact Person Information -->
                <fieldset class="border border-slate-200 rounded-lg p-5 pt-3 mb-8 shadow-sm">
                    <legend class="text-base font-semibold text-indigo-700 px-2 py-1 bg-slate-50 rounded-md tracking-wide">Contact Person Information</legend>
                    <div class="space-y-5 mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-5">
                            <div class="detail-item"><strong>Salutation:</strong> <span>{{ $survey->contact_salutation ?? 'N/A' }}</span></div>
                            <div class="md:col-span-2 detail-item"><strong>Contact Name:</strong> <span>{{ $survey->contact_name ?? 'N/A' }}</span></div>
                        </div>
                        <div class="detail-item"><strong>Job Title:</strong> <span>{{ $survey->contact_job_title ?? 'N/A' }}</span></div>
                        <div class="detail-item"><strong>Department:</strong> <span>{{ $survey->department ?? 'N/A' }}</span></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <div class="detail-item"><strong>Mobile Phone:</strong> <span>{{ $survey->contact_mobile ?? 'N/A' }}</span></div>
                            <div class="detail-item"><strong>Email:</strong> <span>{{ $survey->contact_email ?? 'N/A' }}</span></div>
                        </div>
                        <div class="detail-item"><strong>Status of Lead:</strong> <span>{{ $survey->status_lead ?? 'N/A' }}</span></div>
                        <div class="detail-item"><strong>Potential Revenue (Est.):</strong> <span>{{ $survey->potential_revenue ?? 'N/A' }}</span></div>
                    </div>
                </fieldset>

                <!-- Visit Details & Outcome -->
                <fieldset class="border border-slate-200 rounded-lg p-5 pt-3 shadow-sm">
                    <legend class="text-base font-semibold text-indigo-700 px-2 py-1 bg-slate-50 rounded-md tracking-wide">Visit Details & Outcome</legend>
                    <div class="space-y-5 mt-4">
                        <div class="detail-item"><strong>Is this person a decision maker?:</strong> <span>{{ $survey->decision_maker_status ?? 'N/A' }}</span></div>
                        <div class="detail-item"><strong>Point Interest:</strong> <span class="whitespace-pre-wrap">{{ $survey->sales_call_outcome ?? 'N/A' }}</span></div>
                        <div class="detail-item"><strong>Next Follow Up Action:</strong> <span>{{ $survey->next_follow_up ?? 'N/A' }}</span></div>
                        <div class="detail-item"><strong>Product/Service Interested In:</strong> <span>{{ $survey->product_interested ?? 'N/A' }}</span></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <div class="detail-item"><strong>Status of Lead:</strong> <span>{{ $survey->status_lead ?? 'N/A' }}</span></div>
                            <div class="detail-item"><strong>Potential Revenue (Est.):</strong> <span>{{ $survey->potential_revenue ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                </fieldset>
                
                <div class="text-center mt-10 text-xs text-gray-500">
                    <p>Survey Token: {{ $survey->survey_token }}</p>
                    <p>Submitted At: {{ $survey->submitted_at ? \Carbon\Carbon::parse($survey->submitted_at)->format('D, d M Y, H:i:s') : 'N/A' }}</p>
                </div>

            @else
                <p class="text-center text-red-500 font-semibold">Feedback details not found.</p>
            @endif
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html> 