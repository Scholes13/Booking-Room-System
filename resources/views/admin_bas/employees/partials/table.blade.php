<!-- Employees Table Content -->
<div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
    <!-- Desktop and larger tablet version (hidden on smaller screens) -->
    <table class="w-full text-sm text-left hidden md:table">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
            <tr>
                <th scope="col" class="px-5 py-3.5">No</th>
                <th scope="col" class="px-5 py-3.5">Name</th>
                <th scope="col" class="px-5 py-3.5">Gender</th>
                <th scope="col" class="px-5 py-3.5">Department</th>
                <th scope="col" class="px-5 py-3.5">Position</th>
                <th scope="col" class="px-5 py-3.5 hidden lg:table-cell">Phone/WA</th>
                <th scope="col" class="px-5 py-3.5 hidden lg:table-cell">Email</th>
                <th scope="col" class="px-5 py-3.5 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $index => $employee)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-5 py-4">{{ $employees->firstItem() + $index }}</td>
                <td class="px-5 py-4 font-medium text-gray-900">{{ $employee->name }}</td>
                <td class="px-5 py-4">
                    <span class="px-2.5 py-0.5 inline-flex text-xs font-medium rounded-full {{ $employee->gender == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                        {{ $employee->gender_label }}
                    </span>
                </td>
                <td class="px-5 py-4">{{ $employee->department->name }}</td>
                <td class="px-5 py-4">
                    @php
                    $position = $employee->position ?? '-';
                    $positionClasses = '';
                    $paddingClass = 'px-2.5 py-0.5';
                    
                    if (strpos($position, 'CEO') !== false) {
                        $positionClasses = 'bg-purple-100 text-purple-800';
                    } elseif (strpos($position, 'Managing Director') !== false) {
                        $positionClasses = 'bg-indigo-100 text-indigo-800';
                        $paddingClass = 'px-3 py-1 whitespace-nowrap';
                    } elseif (strpos($position, 'Coordinator') !== false) {
                        $positionClasses = 'bg-blue-100 text-blue-800';
                    } elseif (strpos($position, 'Staff') !== false) {
                        $positionClasses = 'bg-green-100 text-green-800';
                    } else {
                        $positionClasses = 'bg-gray-100 text-gray-800';
                    }
                    @endphp
                    
                    @if($position != '-')
                        <span class="{{ $paddingClass }} {{ $positionClasses }} text-xs font-medium rounded-full inline-flex">
                            {{ $position }}
                        </span>
                    @else
                        <span class="text-gray-500">-</span>
                    @endif
                </td>
                <td class="px-5 py-4 text-gray-500 hidden lg:table-cell">{{ $employee->phone ?? '-' }}</td>
                <td class="px-5 py-4 text-gray-500 hidden lg:table-cell">{{ $employee->email ?? '-' }}</td>
                <td class="px-5 py-4 text-right">
                    <div class="flex items-center justify-end space-x-2">
                        <a href="{{ route('bas.employees.edit', $employee->id) }}" class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                        <button type="button" class="delete-employee px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center" data-id="{{ $employee->id }}">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-5 py-10 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No Data Found</h3>
                        <p class="text-gray-500 text-sm">Try changing your search criteria or adding a new employee</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Mobile Card View (displayed only on mobile screens) -->
    <div class="md:hidden divide-y divide-gray-200">
        @forelse($employees as $index => $employee)
        <div class="p-4 bg-white hover:bg-gray-50">
            <div class="flex flex-col gap-3">
                <!-- Employee number and name -->
                <div class="flex justify-between items-center flex-wrap gap-2">
                    <span class="text-xs text-gray-500">Employee #{{ $employees->firstItem() + $index }}</span>
                    <span class="px-2.5 py-0.5 inline-flex text-xs font-medium rounded-full {{ $employee->gender == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                        {{ $employee->gender_label }}
                    </span>
                </div>
                
                <!-- Name -->
                <div class="font-medium text-gray-900 text-lg break-words">{{ $employee->name }}</div>
                
                <!-- Department -->
                <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-sm">
                    <div class="text-gray-500">Department:</div>
                    <div class="font-medium break-words">{{ $employee->department->name }}</div>
                    
                    <!-- Position -->
                    <div class="text-gray-500">Position:</div>
                    <div>
                        @php
                        $position = $employee->position ?? '-';
                        $positionClasses = '';
                        $paddingClass = 'px-2 py-0.5';
                        
                        if (strpos($position, 'CEO') !== false) {
                            $positionClasses = 'bg-purple-100 text-purple-800';
                        } elseif (strpos($position, 'Managing Director') !== false) {
                            $positionClasses = 'bg-indigo-100 text-indigo-800';
                        } elseif (strpos($position, 'Coordinator') !== false) {
                            $positionClasses = 'bg-blue-100 text-blue-800';
                        } elseif (strpos($position, 'Staff') !== false) {
                            $positionClasses = 'bg-green-100 text-green-800';
                        } else {
                            $positionClasses = 'bg-gray-100 text-gray-800';
                        }
                        @endphp
                        
                        @if($position != '-')
                            <span class="{{ $paddingClass }} {{ $positionClasses }} text-xs font-medium rounded-full inline-flex break-words">
                                {{ $position }}
                            </span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </div>
                    
                    <!-- Contact Information -->
                    @if($employee->phone)
                    <div class="text-gray-500">Phone:</div>
                    <div class="break-all">{{ $employee->phone }}</div>
                    @endif
                    
                    @if($employee->email)
                    <div class="text-gray-500">Email:</div>
                    <div class="break-all">{{ $employee->email }}</div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 mt-2">
                    <a href="{{ route('bas.employees.edit', $employee->id) }}" class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <button type="button" class="delete-employee px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center" data-id="{{ $employee->id }}">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No Data Found</h3>
                <p class="text-gray-500 text-sm">Try changing your search criteria or adding a new employee</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Pagination -->
<div class="flex flex-col md:flex-row justify-between items-center gap-4">
    <div class="text-sm text-gray-600 flex items-center bg-gray-50 px-3 py-2 rounded-lg w-full md:w-auto">
        <svg class="w-4 h-4 mr-1.5 flex-shrink-0 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
        </svg>
        <div class="truncate">
            <span class="hidden sm:inline">Showing</span> 
            <span class="font-medium text-dark px-1">{{ $employees->firstItem() ?? 0 }}</span>
            <span class="hidden sm:inline">to</span><span class="sm:hidden">-</span>
            <span class="font-medium text-dark px-1">{{ $employees->lastItem() ?? 0 }}</span>
            <span class="hidden sm:inline">of</span>
            <span class="font-medium text-dark px-1">{{ $employees->total() ?? 0 }}</span>
            <span class="hidden sm:inline">entries</span>
        </div>
    </div>
    
    @if($employees->hasPages())
    <div class="flex items-center gap-1 flex-wrap justify-center">
        @if ($employees->onFirstPage())
            <span class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center border border-gray-200 rounded-lg bg-gray-50 text-gray-400 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </span>
        @else
            <a href="{{ $employees->appends(request()->except('page'))->previousPageUrl() }}" class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        @endif

        @php
            // Adjust the visible page range based on screen size
            $visiblePages = request()->header('X-Mobile-Device') ? 2 : 5;
            $halfVisible = floor($visiblePages / 2);
            
            $startPage = max($employees->currentPage() - $halfVisible, 1);
            $endPage = min($startPage + $visiblePages - 1, $employees->lastPage());
            
            if ($endPage - $startPage < ($visiblePages - 1) && $startPage > 1) {
                $startPage = max($endPage - ($visiblePages - 1), 1);
            }
        @endphp
        
        @if ($startPage > 1)
            <a href="{{ $employees->appends(request()->except('page'))->url(1) }}" class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-800">1</a>
            @if ($startPage > 2)
                <span class="text-gray-500 w-5 flex justify-center">...</span>
            @endif
        @endif

        @for ($i = $startPage; $i <= $endPage; $i++)
            @if ($i == $employees->currentPage())
                <span class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center border border-[#24448c] bg-[#24448c] text-white rounded-lg">{{ $i }}</span>
            @else
                <a href="{{ $employees->appends(request()->except('page'))->url($i) }}" class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-800">{{ $i }}</a>
            @endif
        @endfor

        @if ($endPage < $employees->lastPage())
            @if ($endPage < $employees->lastPage() - 1)
                <span class="text-gray-500 w-5 flex justify-center">...</span>
            @endif
            <a href="{{ $employees->appends(request()->except('page'))->url($employees->lastPage()) }}" class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-800">{{ $employees->lastPage() }}</a>
        @endif

        @if ($employees->hasMorePages())
            <a href="{{ $employees->appends(request()->except('page'))->nextPageUrl() }}" class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        @else
            <span class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center border border-gray-200 rounded-lg bg-gray-50 text-gray-400 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </span>
        @endif
    </div>
    @endif
</div> 