<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class UserController extends Controller
{
    private function ensureAdmin(): void
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage users.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $users = User::query()
            ->with('roles')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $this->ensureAdmin();

        $user->load(['roles', 'enrollments.course']);

        $currentRole = $user->roles->first()?->name ?? 'student';

        return view('admin.users.show', compact('user', 'currentRole'));
    }

    public function updateRole(Request $request, User $user)
    {
        $this->ensureAdmin();

        $valid = $request->validate([
            'role' => ['required', 'in:student,facilitator,admin'],
        ]);

        $role = Role::firstOrCreate(
            ['name' => $valid['role'], 'guard_name' => 'web'],
            ['guard_name' => 'web']
        );

        $user->roles()->sync([$role->id]);

        return redirect()->route('admin.users.show', $user)->with('message', 'Role updated to ' . $valid['role'] . '.');
    }
}
