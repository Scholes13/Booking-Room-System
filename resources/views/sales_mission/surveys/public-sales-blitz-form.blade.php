<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Blitz Feedback Form</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            opacity: 0.8;
            cursor: pointer;
            transition: opacity 0.2s ease-in-out;
            filter: invert(0.3) sepia(0.8) saturate(5) hue-rotate(200deg);
        }
        input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
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
    </style>
</head>
<body class="bg-slate-100 text-gray-800 text-sm"> 
    <div class="max-w-2xl mx-auto py-8 sm:py-12 px-4 sm:px-6 lg:px-8"> 
        
        <div class="bg-indigo-600 p-6 sm:p-8 rounded-t-xl shadow-lg">
            <h1 class="text-2xl sm:text-3xl font-bold text-white text-center">Sales Blitz - Feedback & Report</h1>
        </div>

        <div class="bg-white shadow-xl rounded-b-xl p-6 sm:p-8">
            <p class="text-gray-600 mb-8 text-center text-xs sm:text-sm">Please fill out this form to report the details and outcome of your sales blitz activity.</p>

            <form action="{{ route('sales_mission.surveys.public.sales_blitz_submit') }}" method="POST" class="space-y-10"> 
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-600 text-red-700 p-4 rounded-md shadow-sm" role="alert">
                        <p class="font-bold text-sm">Oops! Some details need your attention.</p>
                        <ul class="mt-2 list-disc list-inside text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-600 text-green-700 p-4 rounded-md shadow-sm" role="alert">
                        <p class="font-semibold text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                <fieldset class="border border-slate-200 rounded-lg p-5 pt-3 shadow-sm hover:shadow-md transition-shadow duration-200 ease-in-out">
                    <legend class="text-base font-semibold text-indigo-700 px-2 py-1 bg-slate-50 rounded-md tracking-wide">Sales Blitz Information</legend>
                    <div class="space-y-5 mt-4">
                        <div>
                            <label for="blitz_team_id" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Team Name / Sales Person <span class="text-red-600">*</span></label>
                            <select id="blitz_team_id" name="blitz_team_id" required 
                                    class="mt-1 block w-full py-2.5 px-4 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out appearance-none">
                                <option value="">Select Team/Sales Person</option>
                                @if(isset($teams) && $teams->count() > 0)
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('blitz_team_id') == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('blitz_team_id') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="blitz_company_name" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Company Visited <span class="text-red-600">*</span></label>
                            <input type="text" id="blitz_company_name" name="blitz_company_name" value="{{ old('blitz_company_name') }}" required 
                                   class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out">
                            @error('blitz_company_name') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5"> 
                            <div>
                                <label for="blitz_visit_start_datetime" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Visit Start Time <span class="text-red-600">*</span></label>
                                <input type="datetime-local" id="blitz_visit_start_datetime" name="blitz_visit_start_datetime" value="{{ old('blitz_visit_start_datetime') }}" required 
                                       class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out">
                                @error('blitz_visit_start_datetime') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="blitz_visit_end_datetime" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Visit End Time <span class="text-red-600">*</span></label>
                                <input type="datetime-local" id="blitz_visit_end_datetime" name="blitz_visit_end_datetime" value="{{ old('blitz_visit_end_datetime') }}" required 
                                       class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out">
                                @error('blitz_visit_end_datetime') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border border-slate-200 rounded-lg p-5 pt-3 shadow-sm hover:shadow-md transition-shadow duration-200 ease-in-out">
                    <legend class="text-base font-semibold text-indigo-700 px-2 py-1 bg-slate-50 rounded-md tracking-wide">Contact Person Information</legend>
                    <div class="space-y-5 mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-5">
                            <div>
                                <label for="contact_salutation" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Salutation</label>
                                <select id="contact_salutation" name="contact_salutation" 
                                        class="mt-1 block w-full py-2.5 px-4 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out appearance-none">
                                    <option value="" {{ old('contact_salutation') == '' ? 'selected' : '' }}>Select</option>
                                    <option value="Mr." {{ old('contact_salutation') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                    <option value="Ms." {{ old('contact_salutation') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                    <option value="Mrs." {{ old('contact_salutation') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                    <option value="Mdm." {{ old('contact_salutation') == 'Mdm.' ? 'selected' : '' }}>Mdm.</option>
                                    <option value="Dr." {{ old('contact_salutation') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                    <option value="Prof." {{ old('contact_salutation') == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                                </select>
                                @error('contact_salutation') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="contact_name" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Contact Name <span class="text-red-600">*</span></label>
                                <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" required 
                                       class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out">
                                @error('contact_name') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label for="contact_job_title" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Job Title</label>
                            <input type="text" id="contact_job_title" name="contact_job_title" value="{{ old('contact_job_title') }}" 
                                   class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out">
                            @error('contact_job_title') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="department" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Department</label>
                            <input type="text" id="department" name="department" value="{{ old('department') }}" 
                                   class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out">
                            @error('department') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <div>
                                <label for="contact_mobile" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Mobile Phone</label>
                                <input type="text" id="contact_mobile" name="contact_mobile" value="{{ old('contact_mobile') }}" 
                                       class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out"
                                       inputmode="tel">
                                @error('contact_mobile') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="contact_email" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Email</label>
                                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" 
                                       class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out">
                                @error('contact_email') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border border-slate-200 rounded-lg p-5 pt-3 shadow-sm hover:shadow-md transition-shadow duration-200 ease-in-out">
                    <legend class="text-base font-semibold text-indigo-700 px-2 py-1 bg-slate-50 rounded-md tracking-wide">Visit Details & Outcome</legend>
                    <div class="space-y-5 mt-4">
                         <div>
                            <label for="decision_maker_status" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Is this person a decision maker?</label>
                            <select id="decision_maker_status" name="decision_maker_status" 
                                    class="mt-1 block w-full py-2.5 px-4 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out appearance-none">
                                <option value="" {{ old('decision_maker_status') == '' ? 'selected' : '' }}>Select Status</option>
                                <option value="Yes" {{ old('decision_maker_status') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('decision_maker_status') == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Partial" {{ old('decision_maker_status') == 'Partial' ? 'selected' : '' }}>Partial (Contributor)</option>
                                <option value="Unknown" {{ old('decision_maker_status') == 'Unknown' ? 'selected' : '' }}>Unknown</option>
                            </select>
                            @error('decision_maker_status') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="sales_call_outcome" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Point Interest <span class="text-red-600">*</span></label>
                            <textarea id="sales_call_outcome" name="sales_call_outcome" rows="3" required
                                      class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out">{{ old('sales_call_outcome') }}</textarea>
                            @error('sales_call_outcome') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="next_follow_up" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Next Follow Up Action <span class="text-red-600">*</span></label>
                            <input type="text" id="next_follow_up" name="next_follow_up" value="{{ old('next_follow_up') }}" required
                                   class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out">
                            @error('next_follow_up') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="product_interested" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Product/Service Interested In</label>
                            <input type="text" id="product_interested" name="product_interested" value="{{ old('product_interested') }}" 
                                   class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out">
                            @error('product_interested') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <div>
                                <label for="status_lead" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Status of Lead <span class="text-red-600">*</span></label>
                                 <select id="status_lead" name="status_lead" required 
                                         class="mt-1 block w-full py-2.5 px-4 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out appearance-none">
                                    <option value="" {{ old('status_lead') == '' ? 'selected' : '' }}>Select Status</option>
                                    <option value="Hot" {{ old('status_lead') == 'Hot' ? 'selected' : '' }}>Hot (High Interest, immediate potential)</option>
                                    <option value="Warm" {{ old('status_lead') == 'Warm' ? 'selected' : '' }}>Warm (Interested, potential in near future)</option>
                                    <option value="Cold" {{ old('status_lead') == 'Cold' ? 'selected' : '' }}>Cold (Low interest, long-term potential)</option>
                                    <option value="Not Interested" {{ old('status_lead') == 'Not Interested' ? 'selected' : '' }}>Not Interested</option>
                                    <option value="Follow-up Required" {{ old('status_lead') == 'Follow-up Required' ? 'selected' : '' }}>Follow-up Required</option>
                                </select>
                                @error('status_lead') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="potential_revenue" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Potential Revenue (Est.)</label>
                                <input type="text" id="potential_revenue" name="potential_revenue" value="{{ old('potential_revenue') }}" placeholder="e.g., 5000000 or 5M - 10M" 
                                       class="mt-1 block w-full py-2.5 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-700 transition duration-150 ease-in-out"
                                       inputmode="numeric" pattern="[0-9,]*">
                                @error('potential_revenue') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="flex justify-center pt-4"> 
                     <button type="submit" 
                             class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 ease-in-out transform hover:scale-105 active:scale-95">
                         Submit Report
                     </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const potentialRevenueInput = document.getElementById('potential_revenue');
            if (potentialRevenueInput) {
                potentialRevenueInput.addEventListener('input', function(e) {
                    const originalValue = e.target.value;
                    // Hanya pertahankan digit (0-9)
                    let numericString = originalValue.replace(/[^0-9]/g, '');

                    if (numericString.length > 0) {
                        const number = parseInt(numericString, 10);
                        // Cek apakah parsing berhasil (seharusnya selalu berhasil jika numericString hanya berisi digit)
                        if (!isNaN(number)) {
                            e.target.value = number.toLocaleString('en-US');
                        } else {
                            // Fallback jika terjadi hal aneh, kosongkan field
                            e.target.value = ''; 
                        }
                    } else {
                        // Jika numericString kosong (misal, pengguna mengetik huruf atau menghapus semua digit)
                        e.target.value = '';
                    }
                });

                const form = potentialRevenueInput.closest('form');
                if (form) {
                    form.addEventListener('submit', function(event) {
                        const formattedValue = potentialRevenueInput.value;
                        const rawValue = formattedValue.replace(/,/g, '');
                        potentialRevenueInput.value = rawValue;
                    });
                }
            }

            const contactMobileInput = document.getElementById('contact_mobile');
            if (contactMobileInput) {
                contactMobileInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    let startsWithPlus = false;

                    // Cek apakah diawali dengan +
                    if (value.startsWith('+')) {
                        startsWithPlus = true;
                        value = value.substring(1); // Ambil sisa string setelah +
                    }

                    // Hapus semua karakter non-digit dari sisa string
                    let numericPart = value.replace(/[^0-9]/g, '');

                    // Gabungkan kembali
                    e.target.value = (startsWithPlus ? '+' : '') + numericPart;
                });
            }
        });
    </script>
</body>
</html> 