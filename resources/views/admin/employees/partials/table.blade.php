<!-- Employees Table Content -->
<div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="text-sm text-dark">
            <tr class="border-b border-border">
                <th scope="col" class="px-4 py-3">No</th>
                <th scope="col" class="px-4 py-3">Nama</th>
                <th scope="col" class="px-4 py-3">Jenis Kelamin</th>
                <th scope="col" class="px-4 py-3">Departemen</th>
                <th scope="col" class="px-4 py-3">Jabatan</th>
                <th scope="col" class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $index => $employee)
            <tr class="border-b border-border">
                <td class="px-4 py-4">{{ $employees->firstItem() + $index }}</td>
                <td class="px-4 py-4 font-medium">{{ $employee->name }}</td>
                <td class="px-4 py-4">
                    <span class="px-2 py-1 {{ $employee->gender == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }} text-xs rounded-full">
                        {{ $employee->gender_label }}
                    </span>
                </td>
                <td class="px-4 py-4">{{ $employee->department->name }}</td>
                <td class="px-4 py-4">
                    @php
                    $position = $employee->position ?? '-';
                    $positionClasses = '';
                    $paddingClass = 'px-2 py-1';
                    
                    if (strpos($position, 'CEO') !== false) {
                        $positionClasses = 'bg-purple-100 text-purple-800';
                    } elseif (strpos($position, 'Managing Director') !== false) {
                        $positionClasses = 'bg-indigo-100 text-indigo-800';
                        $paddingClass = 'px-3 py-1.5 whitespace-nowrap';
                    } elseif (strpos($position, 'Coordinator') !== false) {
                        $positionClasses = 'bg-blue-100 text-blue-800';
                    } elseif (strpos($position, 'Staff') !== false) {
                        $positionClasses = 'bg-green-100 text-green-800';
                    }
                    @endphp
                    
                    @if($position != '-')
                        <span class="{{ $paddingClass }} {{ $positionClasses }} text-xs rounded-full inline-block">
                            {{ $position }}
                        </span>
                    @else
                        {{ $position }}
                    @endif
                </td>
                <td class="px-4 py-4 text-right">
                    <div class="flex flex-col items-end space-y-1">
                        <a href="{{ route('admin.employees.edit', $employee->id) }}" class="text-accent font-medium text-sm hover:underline">Edit</a>
                        <button type="button" class="delete-employee text-danger font-medium text-sm hover:underline" data-id="{{ $employee->id }}">Hapus</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="border-b border-border">
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                    Belum ada data karyawan. Silahkan tambahkan karyawan baru.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4 flex items-center justify-between">
    <div class="text-sm text-gray-500">
        Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} results
    </div>

    @if($employees->hasPages())
    <div class="flex items-center rounded-md overflow-hidden">
        <!-- Previous Page Link -->
        @if($employees->onFirstPage())
            <span class="flex items-center justify-center h-10 w-10 bg-gray-100 text-gray-400">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </span>
        @else
            <a href="{{ $employees->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" 
                class="flex items-center justify-center h-10 w-10 bg-white border-r border-gray-200 hover:bg-gray-50 text-gray-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </a>
        @endif

        <!-- Pagination Elements -->
        @php
            $start = $employees->currentPage() - 2 <= 0 ? 1 : $employees->currentPage() - 2;
            $end = $employees->lastPage() - $start >= 4 ? $start + 4 : $employees->lastPage();
            if ($end - $start < 4 && $start > 1) {
                $start = max(1, $end - 4);
            }
        @endphp
        
        @for ($i = $start; $i <= $end; $i++)
            @if ($i == $employees->currentPage())
                <span class="flex items-center justify-center h-10 w-10 text-primary font-bold border-r border-gray-200 bg-primary bg-opacity-10">
                    {{ $i }}
                </span>
            @else
                <a href="{{ $employees->url($i) . '&' . http_build_query(request()->except('page')) }}" 
                    class="flex items-center justify-center h-10 w-10 bg-white border-r border-gray-200 hover:bg-gray-50 text-gray-700">
                    {{ $i }}
                </a>
            @endif
        @endfor

        <!-- Next Page Link -->
        @if ($employees->hasMorePages())
            <a href="{{ $employees->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" 
                class="flex items-center justify-center h-10 w-10 bg-white hover:bg-gray-50 text-gray-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </a>
        @else
            <span class="flex items-center justify-center h-10 w-10 bg-gray-100 text-gray-400">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </span>
        @endif
    </div>
    @endif
</div> 