<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaderboardController extends Controller
{
    public function indexOld(Request $request)
    {
        $filter = $request->get('filter', 'day');
        $query = Activity::query();

        if ($filter === 'day') {
            $query->whereDate('activity_date', today());
        } elseif ($filter === 'month') {
            $query->whereMonth('activity_date', now()->month);
        } elseif ($filter === 'year') {
            $query->whereYear('activity_date', now()->year);
        }

        $activities = $query->get()->groupBy('user_id')->map(function($items, $userId) {
            return [
                'user_id' => $userId,
                'full_name' => $items->first()->user->name,
                'total_points' => $items->sum('points')
            ];
        })->sortByDesc('total_points')->values();

        $ranked = $activities->map(function($user, $index) {
            $user['rank'] = $index + 1;
            return $user;
        });
        
        return view('leaderboard', ['users' => $ranked]);
    }

    public function index(Request $request)
    {

        $filter = $request->query('filter', 'day');
        $userId = $request->query('user_id');

        $dateFilter = match ($filter) {
            'day' => now()->startOfDay(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfDay(),
        };

        $query = Activity::where('activity_date', '>=', $dateFilter)
            ->select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('total_points');

        $data = $query->get();

        $ranked = $data->map(function ($item, $index) use ($userId) {
            return [
                'id' => $item->user->id,
                'name' => $item->user->name,
                'points' => $item->total_points,
                'rank' => $index + 1,
                'highlight' => $item->user->id == $userId,
            ];
        });

        return view('leaderboard', ['users' => $ranked]);

        // return response()->json($ranked);
    }

    public function recalculate()
    {
        $activities = Activity::all();

        foreach ($activities as $activity) {
            $user = User::find($activity->user_id);
            if ($user) {
                $user->points += $activity->points;
                $user->save();
            }
        }

        return redirect()->back()->with('success', 'Points recalculated successfully.');
    }
}
