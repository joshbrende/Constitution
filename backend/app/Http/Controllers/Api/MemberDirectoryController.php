<?php

namespace App\Http\Controllers\Api;

use App\Enums\MembershipStanding;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MembershipStandingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Members
 *
 * National full-member directory (minimal fields).
 */
class MemberDirectoryController extends Controller
{
    public function __construct(
        protected MembershipStandingService $membershipStanding,
    ) {}

    /**
     * Search full members
     *
     * @authenticated
     * @queryParam q string optional Name, surname, or membership number.
     * @queryParam province_id integer optional Province filter.
     * @queryParam wing string optional Active league/wing: main|youth|women|veterans.
     * @queryParam page integer optional Page number.
     * @queryParam per_page integer optional Results per page (max 50). Example: 25
     */
    public function index(Request $request): JsonResponse
    {
        $viewer = $request->user();
        if (! $viewer || ! $this->membershipStanding->isFullMember($viewer)) {
            return response()->json([
                'error' => 'forbidden',
                'code' => 'FULL_MEMBER_REQUIRED',
                'message' => 'Only full members can browse the member directory.',
            ], 403);
        }

        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'wing' => ['nullable', 'string', 'in:main,youth,women,veterans'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = User::query()
            ->where('membership_standing', MembershipStanding::Member->value)
            ->with([
                'province:id,name',
                'memberships' => fn ($q) => $q->where('status', 'active')->orderBy('wing'),
            ])
            ->orderBy('surname')
            ->orderBy('name');

        if (! empty($data['q'])) {
            $q = trim($data['q']);
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('surname', 'like', "%{$q}%")
                    ->orWhere('membership_number', 'like', "%{$q}%");
            });
        }

        if (! empty($data['province_id'])) {
            $query->where('province_id', (int) $data['province_id']);
        }

        if (! empty($data['wing'])) {
            $wing = $data['wing'];
            $query->whereHas('memberships', function ($m) use ($wing) {
                $m->where('wing', $wing)->where('status', 'active');
            });
        }

        $perPage = (int) ($data['per_page'] ?? 25);
        $page = $query->paginate($perPage);

        $mapped = $page->getCollection()->map(function (User $user) {
            $activeWings = $user->memberships
                ->map(fn ($m) => strtolower((string) $m->wing))
                ->values()
                ->all();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'membership_number' => $user->membership_number,
                'wing' => $user->wing,
                'active_wings' => $activeWings,
                'province' => $user->province
                    ? ['id' => $user->province->id, 'name' => $user->province->name]
                    : null,
            ];
        });

        $page->setCollection($mapped);

        return response()->json([
            'data' => $page->items(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ]);
    }
}
