@extends('admin.layout')

@section('title', 'Edit Lead Worksheet')

@section('content')
<div class="p-6">
    <form action="{{ route('lead.worksheets.update', $worksheet) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Edit Worksheet</h1>
                <p class="text-gray-600">Prospect: {{ $worksheet->feedbackSurvey->blitz_company_name ?? $worksheet->feedbackSurvey->teamAssignment?->activity?->salesMissionDetail?->company_name }}</p>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-semibold shadow-md">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Main Edit Form -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border">
                <h3 class="text-xl font-semibold mb-6 border-b pb-4">Lead Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Project Name -->
                    <div>
                        <label for="project_name" class="block text-sm font-medium text-gray-700">Project Name</label>
                        <input type="text" name="project_name" id="project_name" value="{{ old('project_name', $worksheet->project_name) }}" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm">
                    </div>
                    <!-- Jenis Layanan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($serviceTypeOptions as $option)
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="service_type_{{ $loop->index }}" name="service_type[]" type="checkbox" value="{{ $option }}" 
                                           @if(is_array(old('service_type', $worksheet->service_type)) && in_array($option, old('service_type', $worksheet->service_type))) checked @endif
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="service_type_{{ $loop->index }}" class="font-medium text-gray-700">{{ $option }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Business Purpose -->
                    <div>
                        <label for="line_of_business" class="block text-sm font-medium text-gray-700">Business Purpose</label>
                        <input type="text" name="line_of_business" id="line_of_business" value="{{ old('line_of_business', $worksheet->line_of_business) }}" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm">
                    </div>
                    <!-- Status of Lead (Editable) -->
                    <div>
                        <label for="current_status" class="block text-sm font-medium text-gray-700">Status of Lead</label>
                        <select id="current_status" name="current_status" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm">
                            @foreach($statusOptions as $status)
                                <option value="{{ $status }}" {{ old('current_status', $worksheet->current_status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Original from survey: {{ $worksheet->feedbackSurvey->status_lead ?? 'N/A' }}</p>
                    </div>
                    <!-- PIC Lead -->
                    <div>
                        <label for="pic_employee_id" class="block text-sm font-medium text-gray-700">PIC Lead</label>
                        <select id="pic_employee_id" name="pic_employee_id" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Select PIC --</option>
                            @foreach($picOptions as $employee)
                                <option value="{{ $employee->id }}" {{ old('pic_employee_id', $worksheet->pic_employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- FollowUp Lead -->
                    <div>
                        <label for="follow_up_status" class="block text-sm font-medium text-gray-700">FollowUp Lead</label>
                        <select id="follow_up_status" name="follow_up_status" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm">
                            @foreach($followUpStatusOptions as $status)
                                <option value="{{ $status }}" {{ old('follow_up_status', $worksheet->follow_up_status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Update Follow Up Note -->
                    <div class="md:col-span-2">
                        <label for="update_note" class="block text-sm font-medium text-gray-700">Update Follow Up Note</label>
                        <textarea name="update_note" id="update_note" rows="3" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm" placeholder="Add a note for the status change or any progress..."></textarea>
                        @error('update_note')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Requirements Lead -->
                    <div class="md:col-span-2">
                        <label for="requirements" class="block text-sm font-medium text-gray-700">Requirements Lead</label>
                        <textarea name="requirements" id="requirements" rows="3" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm">{{ old('requirements', $worksheet->requirements) }}</textarea>
                    </div>
                    <!-- Estimated Revenue -->
                    <div>
                        <label for="estimated_revenue" class="block text-sm font-medium text-gray-700">Estimated Revenue</label>
                        <input type="number" step="0.01" name="estimated_revenue" id="estimated_revenue" value="{{ old('estimated_revenue', $worksheet->estimated_revenue) }}" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm">
                    </div>
                    <!-- Materialized -->
                     <div>
                        <label for="materialized_revenue" class="block text-sm font-medium text-gray-700">Materialized Revenue</label>
                        <input type="number" step="0.01" name="materialized_revenue" id="materialized_revenue" value="{{ old('materialized_revenue', $worksheet->materialized_revenue) }}" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
            </div>

            <!-- Right Column: Logs and Survey Info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Status Log History -->
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <h3 class="text-xl font-semibold mb-4">Status Log History</h3>
                    <div class="space-y-4 max-h-60 overflow-y-auto">
                        @forelse ($worksheet->statusLogs->sortByDesc('created_at') as $log)
                            <div class="border-l-4 pl-4 {{ $loop->first ? 'border-green-500' : 'border-gray-300' }}">
                                <div class="flex justify-between items-center">
                                    <p class="font-semibold text-gray-800">{{ $log->status }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <p class="text-xs text-gray-500">by {{ $log->user->name }}</p>
                                <p class="text-gray-600 mt-1 text-sm">{{ $log->notes }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500">No status logs yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Original Survey Details -->
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <h3 class="text-xl font-semibold mb-4">Original Survey Details</h3>
                     <dl class="grid grid-cols-1 gap-x-4 gap-y-2 text-sm">
                        <div class="col-span-1"><dt class="font-medium text-gray-600">Contact Person:</dt><dd class="text-gray-900">{{ $worksheet->feedbackSurvey->contact_name }}</dd></div>
                        <div class="col-span-1"><dt class="font-medium text-gray-600">Phone:</dt><dd class="text-gray-900">{{ $worksheet->feedbackSurvey->contact_mobile }}</dd></div>
                        <div class="col-span-1"><dt class="font-medium text-gray-600">Email:</dt><dd class="text-gray-900">{{ $worksheet->feedbackSurvey->contact_email }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection 