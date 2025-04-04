@extends('superadmin.layout')

@section('title', 'Manajemen Aktivitas')

@section('content')
<h1 class="text-dark tracking-light text-[32px] font-bold leading-tight px-4 text-left pb-3 pt-6">Manajemen Aktivitas</h1>

<!-- Success Message -->
@if(session('success'))
<div class="m-4 bg-green-500/20 text-green-600 p-3 rounded-md">
    ✅ {{ session('success') }}
</div>
@endif

<!-- Error Message -->
@if($errors->any())
<div class="m-4 bg-red-500/20 text-red-600 p-3 rounded-md">
    <ul>
        @foreach($errors->all() as $error)
        <li>⚠️ {{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="px-4 py-3 @container">
    <div id="loadingOverlay" class="hidden absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 border-t-2 border-b-2 border-primary rounded-full animate-spin"></div>
            <p class="text-primary font-medium">Memuat data...</p>
        </div>
    </div>
    
    <div class="flex justify-between items-center mb-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('superadmin.activities.index') }}" class="flex min-w-[84px] max-w-[180px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Semua Aktivitas</span>
            </a>
            <a href="{{ route('superadmin.activities.calendar') }}" class="flex min-w-[84px] max-w-[180px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-white border border-primary text-primary text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Kalender</span>
            </a>
        </div>
        <a href="{{ route('superadmin.activities.create') }}" class="flex min-w-[84px] max-w-[180px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            <span class="truncate">Tambah Aktivitas</span>
        </a>
    </div>
    
    <div class="flex overflow-hidden rounded-xl border border-border bg-white">
        <table class="flex-1 min-w-full">
            <thead>
                <tr class="bg-white">
                    <th class="table-column-120 px-4 py-3 text-left text-dark w-[200px] text-sm font-medium leading-normal">Nama</th>
                    <th class="table-column-240 px-4 py-3 text-left text-dark w-[150px] text-sm font-medium leading-normal">Departemen</th>
                    <th class="table-column-360 px-4 py-3 text-left text-dark w-[180px] text-sm font-medium leading-normal">Jenis Aktivitas</th>
                    <th class="table-column-480 px-4 py-3 text-left text-dark w-[180px] text-sm font-medium leading-normal">Lokasi</th>
                    <th class="table-column-600 px-4 py-3 text-left text-dark w-[150px] text-sm font-medium leading-normal">Mulai</th>
                    <th class="table-column-720 px-4 py-3 text-left text-dark w-[150px] text-sm font-medium leading-normal">Selesai</th>
                    <th class="table-column-840 px-4 py-3 text-left text-dark w-[60px] text-sm font-medium leading-normal">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                <tr class="border-t border-t-border hover:bg-gray-50">
                    <td class="table-column-120 h-[72px] px-4 py-2 w-[200px] text-dark text-sm font-normal leading-normal">{{ $activity->name }}</td>
                    <td class="table-column-240 h-[72px] px-4 py-2 w-[150px] text-dark text-sm font-normal leading-normal">{{ $activity->department->name }}</td>
                    <td class="table-column-360 h-[72px] px-4 py-2 w-[180px] text-dark text-sm font-normal leading-normal">{{ $activity->activity_type }}</td>
                    <td class="table-column-480 h-[72px] px-4 py-2 w-[180px] text-dark text-sm font-normal leading-normal">{{ $activity->city }}, {{ $activity->province }}</td>
                    <td class="table-column-600 h-[72px] px-4 py-2 w-[150px] text-dark text-sm font-normal leading-normal">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y H:i') }}</td>
                    <td class="table-column-720 h-[72px] px-4 py-2 w-[150px] text-dark text-sm font-normal leading-normal">{{ \Carbon\Carbon::parse($activity->end_datetime)->format('d M Y H:i') }}</td>
                    <td class="table-column-840 h-[72px] px-4 py-2 w-[60px] text-accent text-sm font-normal leading-normal">
                        <div class="flex gap-2">
                            <a href="{{ route('superadmin.activities.edit', $activity->id) }}" class="text-primary hover:text-primary/80">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('superadmin.activities.delete', $activity->id) }}" method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-danger hover:text-danger/80">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-accent">
                        <div class="flex flex-col items-center py-8">
                            <i class="fas fa-calendar-alt text-accent text-4xl mb-4"></i>
                            <p class="text-lg font-medium">Tidak ada data aktivitas</p>
                            <p class="text-sm text-accent">Silakan tambahkan aktivitas baru</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $activities->links() }}
    </div>
    
    <style>
        @container (max-width: 120px) { .table-column-120 { display: none; } }
        @container (max-width: 240px) { .table-column-240 { display: none; } }
        @container (max-width: 360px) { .table-column-360 { display: none; } }
        @container (max-width: 480px) { .table-column-480 { display: none; } }
        @container (max-width: 600px) { .table-column-600 { display: none; } }
        @container (max-width: 720px) { .table-column-720 { display: none; } }
        @container (max-width: 840px) { .table-column-840 { display: none; } }
    </style>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete confirmations
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi',
                    text: "Anda yakin ingin menghapus aktivitas ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#22428e',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>
@endpush 