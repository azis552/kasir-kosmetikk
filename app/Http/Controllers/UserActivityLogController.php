<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class UserActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->get('search');
        $userId  = $request->get('user_id');
        $date    = $request->get('date', now()->format('Y-m-d'));

        $query = UserActivityLog::with('userDetail')
            ->orderByDesc('created_at');

        // Filter tanggal
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        // Filter user
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Search di action atau details
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
            });
        }

        $logs  = $query->paginate(20)->appends(request()->all());
        $users = User::orderBy('name')->get();
        $title = 'Log Aktivitas User';

        return view('activity_logs.index', compact('logs', 'users', 'date', 'userId', 'search', 'title'));
    }

    public function destroy(Request $request)
    {
        $date = $request->get('date');

        if ($date) {
            UserActivityLog::whereDate('created_at', $date)->delete();
        } else {
            // Hapus log lebih dari 30 hari
            UserActivityLog::where('created_at', '<', now()->subDays(30))->delete();
        }

        return redirect()->route('activity-logs.index')
            ->with('success', $date
                ? "Log tanggal {$date} berhasil dihapus."
                : 'Log lebih dari 30 hari berhasil dihapus.'
            );
    }
}