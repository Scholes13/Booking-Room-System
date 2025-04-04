@php
    $layout = isset($isSuperAdmin) && $isSuperAdmin ? 'superadmin.layout' : 'admin.layout';
@endphp

@extends($layout)

@section('title', 'Detail Activity Log')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Detail Activity Log</h1>
        <a href="{{ route('superadmin.logs.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Umum</h2>
                    <table class="w-full">
                        <tr>
                            <td class="py-2 text-gray-600 font-medium">ID</td>
                            <td class="py-2 text-gray-900">{{ $log->id }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600 font-medium">Admin</td>
                            <td class="py-2 text-gray-900">{{ $log->user->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600 font-medium">Email</td>
                            <td class="py-2 text-gray-900">{{ $log->user->email ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600 font-medium">Aktivitas</td>
                            <td class="py-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($log->action == 'create') bg-green-100 text-green-800
                                    @elseif($log->action == 'update') bg-yellow-100 text-yellow-800
                                    @elseif($log->action == 'delete') bg-red-100 text-red-800
                                    @elseif($log->action == 'export') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600 font-medium">Modul</td>
                            <td class="py-2 text-gray-900">{{ ucfirst(str_replace('_', ' ', $log->module)) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600 font-medium">Waktu</td>
                            <td class="py-2 text-gray-900">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Teknis</h2>
                    <table class="w-full">
                        <tr>
                            <td class="py-2 text-gray-600 font-medium">IP Address</td>
                            <td class="py-2 text-gray-900">{{ $log->ip_address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600 font-medium">User Agent</td>
                            <td class="py-2 text-gray-900 break-words text-sm">{{ $log->user_agent ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Deskripsi</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-900">{{ $log->description }}</p>
                </div>
            </div>

            @if($log->properties)
            <div class="mt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Data</h2>
                <div class="bg-gray-50 p-4 rounded-lg overflow-x-auto">
                    <pre class="text-sm text-gray-800">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 