<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

final class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->orderByDesc('points')
            ->with('badges')
            ->paginate(20);

        return view('leaderboard.index', compact('users'));
    }
}
