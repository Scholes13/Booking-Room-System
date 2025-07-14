<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogService;

class MeetingRoomController extends Controller
{
    /**
     * Helper method untuk mendapatkan view berdasarkan role user
     */
    private function getViewByRole($adminView, $defaultView = null)
    {
        if (session('user_role') === 'superadmin') {
            $superadminView = str_replace('admin.', 'superadmin.', $adminView);
            
            if (view()->exists($superadminView)) {
                return $superadminView;
            }
        }
        
        return $defaultView ?: $adminView;
    }

    public function index()
    {
        $rooms = MeetingRoom::orderBy('name', 'asc')->get();
        $view = $this->getViewByRole('admin.meeting-rooms.index');
        return view($view, compact('rooms'));
    }

    public function create()
    {
        if (Auth::check() && Auth::user()->role === 'admin_bas') {
            return redirect()->route('bas.meeting_rooms');
        }
        
        if (Auth::check() && Auth::user()->role === 'superadmin') {
            return redirect()->route('superadmin.meeting_rooms.index');
        }

        return redirect()->route('admin.meeting_rooms.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'projector' => 'required|in:Tersedia,Tidak Tersedia',
            'whiteboard' => 'required|in:Tersedia,Tidak Tersedia',
        ]);

        MeetingRoom::create($validated);

        ActivityLogService::logCreate(
            'meeting_rooms', 
            'Menambahkan meeting room baru: ' . $validated['name'],
            $validated
        );

        $routeName = 'admin.meeting_rooms';
        if (session('user_role') === 'superadmin') {
            $routeName = 'superadmin.meeting_rooms';
        }

        return redirect()->route($routeName)->with('success', 'Meeting room berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $room = MeetingRoom::findOrFail($id);
        $view = $this->getViewByRole('admin.meeting-rooms.edit');
        return view($view, compact('room'));
    }

    public function update(Request $request, $id)
    {
        $room = MeetingRoom::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'projector' => 'required|in:Tersedia,Tidak Tersedia',
            'whiteboard' => 'required|in:Tersedia,Tidak Tersedia',
        ]);
        
        $room->update($validated);

        ActivityLogService::logUpdate(
            'meeting_rooms', 
            'Mengupdate meeting room: ' . $room->name,
            $validated
        );

        $routeName = 'admin.meeting_rooms';
        if (session('user_role') === 'superadmin') {
            $routeName = 'superadmin.meeting_rooms';
        }
        
        return redirect()->route($routeName)->with('success', 'Meeting room berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $room = MeetingRoom::findOrFail($id);
        $roomName = $room->name;
        $room->delete();

        ActivityLogService::logDelete(
            'meeting_rooms', 
            'Menghapus meeting room: ' . $roomName,
            ['id' => $id, 'name' => $roomName]
        );

        $routeName = 'admin.meeting_rooms';
        if (session('user_role') === 'superadmin') {
            $routeName = 'superadmin.meeting_rooms';
        }

        return redirect()->route($routeName)->with('success', 'Meeting room berhasil dihapus.');
    }
} 