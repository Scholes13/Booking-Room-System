<div class="flex gap-3 filters-container">
    @php
        $filters = [
            [
                'id' => 'btnToday',
                'onclick' => 'filterToday()',
                'icon' => 'calendar',
                'label' => 'Hari Ini',
                'class' => 'from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800'
            ],
            [
                'id' => 'btnWeek',
                'onclick' => 'filterThisWeek()',
                'icon' => 'calendar-days',
                'label' => 'Minggu Ini',
                'class' => 'from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700'
            ],
            [
                'id' => 'btnHour',
                'onclick' => 'filterHour()',
                'icon' => 'clock',
                'label' => 'Jam Ini',
                'class' => 'from-green-600 to-green-700 hover:from-green-700 hover:to-green-800'
            ],
            [
                'id' => 'btnMonth',
                'onclick' => 'filterThisMonth()',
                'icon' => 'calendar-month',
                'label' => 'Bulan Ini',
                'class' => 'from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800'
            ]
        ];
    @endphp

    @foreach($filters as $filter)
        <button 
            id="{{ $filter['id'] }}" 
            onclick="{{ $filter['onclick'] }}" 
            class="filter-btn flex items-center gap-2 px-4 py-2 bg-gradient-to-r {{ $filter['class'] }} text-white rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105"
            aria-label="Filter booking untuk {{ strtolower($filter['label']) }}"
        >
            @include('admin.dashboard.partials.icons.' . $filter['icon'])
            <span>{{ $filter['label'] }}</span>
        </button>
    @endforeach

    <button 
        onclick="resetFilter()" 
        class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg shadow-lg hover:from-gray-700 hover:to-gray-800 transition-all duration-300 transform hover:scale-105"
        aria-label="Reset semua filter"
    >
        @include('admin.dashboard.partials.icons.reset')
        <span>Reset</span>
    </button>
</div>