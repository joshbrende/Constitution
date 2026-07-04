<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MembershipStanding;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminScopeService;
use Illuminate\View\View;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    public function __construct(
        protected AdminScopeService $adminScope
    ) {}

    public function index(Request $request): View
    {
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);

        // Full members — certificate issued (party register).
        $query = User::query()
            ->where('membership_standing', MembershipStanding::Member->value)
            ->with(['roles', 'certificates', 'province:id,name'])
            ->orderByDesc('id');

        $this->adminScope->applyToUserQuery($query, $admin);

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
