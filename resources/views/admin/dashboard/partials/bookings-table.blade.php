<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-800" id="bookingTable">
        @include('admin.dashboard.partials.table-header')
        @include('admin.dashboard.partials.table-body')
    </table>
</div>

@push('scripts')
<script>
    window.TABLE_CONFIG = {
        searchDelay: 500,
        perPage: {{ $perPage ?? 10 }},
        currentPage: {{ $currentPage ?? 1 }},
    };
</script>
@endpush