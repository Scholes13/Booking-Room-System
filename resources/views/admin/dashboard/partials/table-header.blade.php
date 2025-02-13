<thead>
    <tr class="bg-gradient-to-r from-gray-800 to-gray-900">
        @foreach([
            'Nama' => 'nama',
            'Departemen' => 'department',
            'Tanggal' => 'date',
            'Jam Mulai' => 'start_time',
            'Jam Selesai' => 'end_time',
            'Ruang Meeting' => 'room',
            'Deskripsi' => 'description',
            'Aksi' => 'actions'
        ] as $label => $key)
            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider"
                role="columnheader"
                aria-sort="none"
                data-key="{{ $key }}">
                {{ $label }}
                @if($key !== 'actions')
                    <button class="ml-1 focus:outline-none" onclick="sortTable('{{ $key }}')">
                        <span class="sort-icon invisible">↑</span>
                    </button>
                @endif
            </th>
        @endforeach
    </tr>
</thead>