<?php
namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\LeaderboardUpdated;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'day');
        $userId = $request->get('id');

        $query = Activity::query()
            ->select('user_id', DB::raw('SUM(points) as total_points'))
            ->join('users', 'activities.user_id', '=', 'users.id')
            ->selectRaw('users.name, users.id')
            ->groupBy('user_id', 'users.name', 'users.id');

        if ($filter === 'day') {
            $query->whereDate('activity_date', today());
        } elseif ($filter === 'month') {
            $query->whereMonth('activity_date', now()->month);
        } elseif ($filter === 'year') {
            $query->whereYear('activity_date', now()->year);
        }

        $users = $query->orderByDesc('total_points')->get();

        // Rank logic
        $ranked = $users->values()->map(function ($user, $index) {
            $user->rank = $index + 1;
            return $user;
        });

        // If searching specific user ID
        if ($userId) {
            $searchUser = $ranked->where('id', $userId)->first();
            if ($searchUser) {
                $ranked = $ranked->prepend($ranked->pull($ranked->search(fn($u) => $u->id == $userId)));
            }
        }

        return view('leaderboard', compact('ranked', 'filter', 'userId'));
    }

    public function recalculate()
    {
        event(new LeaderboardUpdated);
        return redirect()->back();
    }

}
