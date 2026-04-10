<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    public function index(Request $request): View
    {
        // "Members" = users who have at least one certificate (completed membership path)
        $query = User::query()
            ->whereHas('certificates')
            ->with(['roles', 'certificates'])
            ->orderByDesc('id');

        // Basic search for admin convenience.
        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('surname', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Keep it lightweight for admin screens; 25 per page is responsive.
        $members = $query->paginate(25)->withQueryString();

        return view('admin.members.index', compact('members'));
    }
}
