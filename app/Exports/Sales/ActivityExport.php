<?php

namespace App\Exports\Sales;

use App\Models\Activity;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $request = $this->request;
        $query = Activity::query()
            ->where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->with(['department', 'salesMissionDetail', 'teamAssignments.team']);

        // Join dengan sales_mission_details untuk sorting/filtering
        $query->join('sales_mission_details', 'activities.id', '=', 'sales_mission_details.activity_id');

        // Filter by search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('salesMissionDetail', function($sq) use ($searchTerm) {
                    $sq->where('sales_mission_details.company_name', 'like', "%{$searchTerm}%")
                       ->orWhere('sales_mission_details.company_pic', 'like', "%{$searchTerm}%")
                       ->orWhere('sales_mission_details.company_contact', 'like', "%{$searchTerm}%");
                })
                ->orWhere('activities.description', 'like', "%{$searchTerm}%")
                ->orWhere('activities.name', 'like', "%{$searchTerm}%")
                ->orWhere('activities.city', 'like', "%{$searchTerm}%")
                ->orWhere('activities.province', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by assignment status
        if ($request->filled('assignment_status')) {
            $assignmentStatus = $request->assignment_status;
            if ($assignmentStatus === 'assigned') {
                $query->whereHas('teamAssignments');
            } elseif ($assignmentStatus === 'not_assigned') {
                $query->whereDoesntHave('teamAssignments');
            }
        }

        // Filter by Location (City)
        if ($request->filled('filter_location')) {
            $query->where('activities.city', $request->filter_location);
        }

        // Filter by a single date (start_date)
        if ($request->filled('start_date')) {
            $selectedDate = Carbon::parse($request->start_date)->toDateString();
            $query->whereDate('activities.start_datetime', $selectedDate);
        }

        // Sorting logic
        $sortBy = $request->input('sort_by', 'activities.start_datetime');
        $sortDirection = $request->input('sort_direction', 'desc');

        $allowedSortColumns = [
            'sales_mission_details.company_name',
            'sales_mission_details.company_pic',
            'activities.city',
            'activities.start_datetime'
        ];
        if (in_array($sortBy, $allowedSortColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('activities.start_datetime', 'desc');
        }

        // Select activities.* untuk menghindari kolom ambigu
        $query->select('activities.*');

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Kegiatan',
            'Nama Perusahaan',
            'PIC Perusahaan',
            'Kota',
            'Provinsi',
            'Tanggal Mulai',
            'Status',
            'Ditugaskan ke Tim',
        ];
    }

    public function map($activity): array
    {
        return [
            $activity->id,
            $activity->name,
            $activity->salesMissionDetail->company_name ?? '-',
            $activity->salesMissionDetail->company_pic ?? '-',
            $activity->city,
            $activity->province,
            Carbon::parse($activity->start_datetime)->format('d-m-Y H:i'),
            $activity->status,
            $activity->teamAssignments->isNotEmpty() ? $activity->teamAssignments->map(fn($ta) => $ta->team->name)->implode(', ') : 'Belum Ditugaskan',
        ];
    }
}
