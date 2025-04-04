<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Report Print</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: white;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        .subtitle {
            font-size: 16px;
            color: #666;
            margin: 5px 0 0;
        }
        .report-metadata {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .metadata-group {
            margin-bottom: 15px;
        }
        .metadata-label {
            font-weight: 600;
            margin-right: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .print-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }
        @media print {
            .print-button {
                display: none;
            }
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="print-button" onclick="window.print()">
            <i class="fas fa-print"></i> Print Report
        </button>
        
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="logo">
            <h1 class="title">Activity Report</h1>
            <p class="subtitle">Generated on {{ now()->format('d F Y, H:i') }}</p>
        </div>
        
        <div class="report-metadata">
            <div>
                <div class="metadata-group">
                    <span class="metadata-label">Report Type:</span>
                    <span>{{ ucfirst(str_replace('_', ' ', $reportType)) }}</span>
                </div>
                <div class="metadata-group">
                    <span class="metadata-label">Time Period:</span>
                    <span>{{ ucfirst($timePeriod) }}</span>
                </div>
            </div>
            <div>
                <div class="metadata-group">
                    <span class="metadata-label">Year:</span>
                    <span>{{ $year }}</span>
                </div>
                @if($timePeriod === 'monthly')
                <div class="metadata-group">
                    <span class="metadata-label">Month:</span>
                    <span>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</span>
                </div>
                @elseif($timePeriod === 'quarterly')
                <div class="metadata-group">
                    <span class="metadata-label">Quarter:</span>
                    <span>Q{{ $quarter }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Report Content based on report type -->
        @if(isset($data['no_data']) && $data['no_data'])
            <div class="text-center" style="padding: 50px 0;">
                <p>No data available for the selected period.</p>
            </div>
        @else
            @if($reportType === 'employee_activity')
                <!-- Summary -->
                <div class="metadata-group">
                    <span class="metadata-label">Total Activities:</span>
                    <span>{{ $data['total_activities'] ?? 0 }}</span>
                </div>
                
                <!-- Employee Activity Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Activity Type</th>
                            <th>Location</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Duration</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($data['activities'] ?? []) as $activity)
                        <tr>
                            <td>{{ $activity->name }}</td>
                            <td>{{ $activity->department }}</td>
                            <td>{{ $activity->category }}</td>
                            <td>{{ $activity->city }}, {{ $activity->province }}</td>
                            <td>{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($activity->end_datetime)->format('d M Y H:i') }}</td>
                            <td>{{ $activity->total_days }}</td>
                            <td>{{ $activity->description }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif($reportType === 'department_activity')
                <!-- Department Activity Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Total Activities</th>
                            <th>Hours Used</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($data['department_stats'] ?? []) as $dept)
                        <tr>
                            <td>{{ $dept['department'] }}</td>
                            <td>{{ $dept['total_activities'] }}</td>
                            <td>{{ $dept['hours_used'] }} hours</td>
                            <td>{{ $dept['percentage'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif($reportType === 'location_activity')
                <!-- Location Activity Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Total Activities</th>
                            <th>Hours Used</th>
                            <th>Meetings</th>
                            <th>Invitations</th>
                            <th>Surveys</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($data['location_stats'] ?? []) as $loc)
                        <tr>
                            <td>{{ $loc['location'] }}</td>
                            <td>{{ $loc['total_activities'] }}</td>
                            <td>{{ $loc['hours_used'] }} hours</td>
                            <td>{{ $loc['activities_by_type']['Meeting'] ?? 0 }}</td>
                            <td>{{ $loc['activities_by_type']['Invitation'] ?? 0 }}</td>
                            <td>{{ $loc['activities_by_type']['Survey'] ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif
        
        <div class="footer">
            <p>This report is generated automatically. For any questions, please contact the administrator.</p>
            <p>&copy; {{ date('Y') }} Your Company Name - All Rights Reserved</p>
        </div>
    </div>
</body>
</html> 