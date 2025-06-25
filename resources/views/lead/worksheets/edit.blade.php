@extends('admin.layout')

@section('title', 'Edit Lead Worksheet')
@section('header', 'Edit Lead Worksheet')

@push('styles')
<style>
    /* Make the overall container have a light background */
    .content {
        background-color: #f4f6f9;
    }

    /* Modern Panel/Card styling */
    .panel {
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        border: none;
        background-color: #fff;
        margin-bottom: 25px;
    }
    .panel-header {
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 15px 20px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    .panel-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
    }
    .panel-body {
        padding: 20px;
    }
    .panel-footer {
        background-color: #f7f9fc;
        border-top: 1px solid #e9ecef;
        padding: 15px 20px;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    
    /* Give form elements more space */
    .form-group {
        margin-bottom: 1.75rem;
    }

    /* Custom tab styling */
    .info-tabs .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }
    .info-tabs .nav-tabs > li {
        margin-bottom: -1px;
    }
    .info-tabs .nav-tabs > li > a {
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        color: #6c757d;
        font-weight: 600;
        padding: .75rem 1.25rem;
    }
    .info-tabs .nav-tabs > li.active > a, 
    .info-tabs .nav-tabs > li > a:hover {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    .info-tabs .tab-content {
        padding-top: 20px;
    }

    /* Timeline-like styling for logs */
    .log-timeline {
        position: relative;
        padding-left: 25px;
    }
    .log-item {
        position: relative;
        border-left: 2px solid #e9ecef;
        padding: 5px 0 20px 20px;
        margin-left: 5px;
    }
    .log-item:last-child {
        padding-bottom: 0;
    }
    .log-item::before {
        content: '';
        position: absolute;
        left: -7px; /* (12px width / 2) + 1px border */
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #adb5bd;
        border: 2px solid #fff;
    }
    .log-item.latest::before {
        background-color: #28a745;
    }
    .log-item p { margin-bottom: 5px; }
    .log-item .log-details { font-size: 0.8rem; color: #888; }
</style>
@endpush

@section('content')
<form action="{{ route('lead.worksheets.update', $worksheet->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Main Form Column -->
        <div class="col-md-8">
            <div class="panel">
                <div class="panel-header">
                    <h3 class="panel-title">
                        Lead Details for: <strong>{{ $worksheet->feedbackSurvey->blitz_company_name ?? $worksheet->feedbackSurvey->teamAssignment?->activity?->salesMissionDetail?->company_name }}</strong>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_name">Project Name</label>
                                <input type="text" name="project_name" id="project_name" class="form-control" value="{{ old('project_name', $worksheet->project_name) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="line_of_business">Line of Business</label>
                                <input type="text" name="line_of_business" id="line_of_business" class="form-control" value="{{ old('line_of_business', $worksheet->line_of_business) }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Service Type</label>
                        <div class="row">
                            @foreach($serviceTypeOptions as $option)
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <input id="service_type_{{ $loop->index }}" name="service_type[]" type="checkbox" value="{{ $option }}" 
                                           @if(is_array(old('service_type', $worksheet->service_type)) && in_array($option, old('service_type', $worksheet->service_type))) checked @endif>
                                    <label for="service_type_{{ $loop->index }}">{{ $option }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="current_status">Status of Lead</label>
                                <select id="current_status" name="current_status" class="form-control">
                                    @foreach($statusOptions as $status)
                                        <option value="{{ $status }}" {{ old('current_status', $worksheet->current_status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Original from survey: {{ $worksheet->feedbackSurvey->status_lead ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pic_employee_id">PIC Lead</label>
                                <select id="pic_employee_id" name="pic_employee_id" class="form-control">
                                    <option value="">-- Select PIC --</option>
                                    @foreach($picOptions as $employee)
                                        <option value="{{ $employee->id }}" {{ old('pic_employee_id', $worksheet->pic_employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="follow_up_status">Follow Up Status</label>
                                <select id="follow_up_status" name="follow_up_status" class="form-control">
                                     <option value="">-- Select Status --</option>
                                    @foreach($followUpStatusOptions as $status)
                                        <option value="{{ $status }}" {{ old('follow_up_status', $worksheet->follow_up_status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                     <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estimated_revenue">Estimated Revenue (IDR)</label>
                                <input type="number" step="1000" name="estimated_revenue" id="estimated_revenue" class="form-control" value="{{ old('estimated_revenue', $worksheet->estimated_revenue) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="materialized_revenue">Materialized Revenue (IDR)</label>
                                <input type="number" step="1000" name="materialized_revenue" id="materialized_revenue" class="form-control" value="{{ old('materialized_revenue', $worksheet->materialized_revenue) }}">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label for="requirements">Requirements</label>
                        <textarea name="requirements" id="requirements" rows="4" class="form-control">{{ old('requirements', $worksheet->requirements) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="update_note">Add New Update Note</label>
                        <textarea name="update_note" id="update_note" rows="4" class="form-control" placeholder="Add a note for the status change or any progress... Required if status is changed."></textarea>
                        @error('update_note')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                 <div class="panel-footer text-right">
                    <a href="{{ route('lead.worksheets.index') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>

        <!-- Side Column with Tabs -->
        <div class="col-md-4">
            <div class="panel info-tabs">
                <div class="panel-header">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#logs" data-toggle="tab">Status History</a></li>
                        <li><a href="#info" data-toggle="tab">Survey Info</a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="logs">
                            <div class="log-timeline">
                                @forelse ($worksheet->statusLogs->sortByDesc('created_at') as $log)
                                    <div class="log-item {{ $loop->first ? 'latest' : '' }}">
                                        <p><strong>{{ $log->status }}</strong></p>
                                        <p class="text-muted">{{ $log->notes }}</p>
                                        <div class="log-details">
                                            <span>by {{ $log->user->name ?? 'System' }}</span> &middot;
                                            <span>{{ $log->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted">No status logs yet.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="tab-pane" id="info">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Contact Person</th>
                                        <td>{{ $worksheet->feedbackSurvey->contact_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{ $worksheet->feedbackSurvey->contact_mobile }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $worksheet->feedbackSurvey->contact_email }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection 