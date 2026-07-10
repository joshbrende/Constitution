<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberNotificationCampaign;
use App\Models\Province;
use App\Models\Role;
use App\Rules\SafeUrlRule;
use App\Services\MemberNotificationDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberNotificationsController extends Controller
{
    public function index(): View
    {
        $campaigns = MemberNotificationCampaign::query()
            ->with(['province', 'role', 'creator'])
            ->orderByDesc('id')
            ->get();

        return view('admin.member-notifications.index', compact('campaigns'));
    }

    public function create(): View
    {
        return view('admin.member-notifications.form', [
            'campaign' => null,
            'provinces' => Province::orderBy('sort_order')->orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, MemberNotificationDispatchService $dispatch): RedirectResponse
    {
        $this->authorize('admin.section', 'member_notifications');
        $data = $this->validateCampaign($request);
        $data['created_by_user_id'] = $request->user()?->id;
        $data['status'] = MemberNotificationCampaign::STATUS_DRAFT;

        $campaign = MemberNotificationCampaign::create($data);

        if ($request->boolean('publish_now')) {
            $count = $dispatch->publish($campaign);

            return redirect()->route('admin.member-notifications.index')
                ->with('success', "Notification published to {$count} member(s).");
        }

        return redirect()->route('admin.member-notifications.index')
            ->with('success', 'Notification saved as draft.');
    }

    public function edit(MemberNotificationCampaign $member_notification): View
    {
        return view('admin.member-notifications.form', [
            'campaign' => $member_notification,
            'provinces' => Province::orderBy('sort_order')->orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MemberNotificationCampaign $member_notification, MemberNotificationDispatchService $dispatch): RedirectResponse
    {
        $this->authorize('admin.section', 'member_notifications');

        if ($member_notification->isPublished()) {
            return redirect()->route('admin.member-notifications.edit', $member_notification)
                ->withErrors(['campaign' => 'Published notifications cannot be edited. Create a new notification instead.']);
        }

        $data = $this->validateCampaign($request);
        $member_notification->update($data);

        if ($request->boolean('publish_now')) {
            $count = $dispatch->publish($member_notification);

            return redirect()->route('admin.member-notifications.index')
                ->with('success', "Notification published to {$count} member(s).");
        }

        return redirect()->route('admin.member-notifications.index')
            ->with('success', 'Notification updated.');
    }

    public function publish(MemberNotificationCampaign $member_notification, MemberNotificationDispatchService $dispatch): RedirectResponse
    {
        $this->authorize('admin.section', 'member_notifications');

        if ($member_notification->isPublished()) {
            return redirect()->route('admin.member-notifications.index')
                ->with('success', 'Notification was already published.');
        }

        $count = $dispatch->publish($member_notification);

        return redirect()->route('admin.member-notifications.index')
            ->with('success', "Notification published to {$count} member(s).");
    }

    public function destroy(MemberNotificationCampaign $member_notification): RedirectResponse
    {
        $this->authorize('admin.section', 'member_notifications');

        if ($member_notification->isPublished()) {
            return redirect()->route('admin.member-notifications.index')
                ->withErrors(['campaign' => 'Published notifications cannot be deleted (members may already have them in their inbox).']);
        }

        $member_notification->delete();

        return redirect()->route('admin.member-notifications.index')
            ->with('success', 'Draft notification deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCampaign(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:5000'],
            'audience_type' => ['required', 'in:all,province,role'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id', 'required_if:audience_type,province'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id', 'required_if:audience_type,role'],
            'cta_type' => ['required', 'in:none,internal,external'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_tab' => ['nullable', 'string', 'max:50'],
            'cta_screen' => ['nullable', 'string', 'max:80'],
            'cta_params_json' => ['nullable', 'string', 'max:2000'],
            'cta_url' => ['nullable', 'string', 'max:500', new SafeUrlRule],
        ]);

        if ($data['audience_type'] !== MemberNotificationCampaign::AUDIENCE_PROVINCE) {
            $data['province_id'] = null;
        }
        if ($data['audience_type'] !== MemberNotificationCampaign::AUDIENCE_ROLE) {
            $data['role_id'] = null;
        }
        if ($data['cta_type'] === 'none') {
            $data['cta_label'] = null;
            $data['cta_tab'] = null;
            $data['cta_screen'] = null;
            $data['cta_url'] = null;
            $data['cta_params'] = null;
        } elseif ($data['cta_type'] === 'external') {
            $data['cta_tab'] = null;
            $data['cta_screen'] = null;
            $data['cta_params'] = null;
        } else {
            $data['cta_url'] = null;
            $paramsJson = $data['cta_params_json'] ?? null;
            $data['cta_params'] = null;
            if ($paramsJson !== null && trim($paramsJson) !== '') {
                $decoded = json_decode($paramsJson, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data['cta_params'] = $decoded;
                }
            }
        }

        unset($data['cta_params_json']);

        return $data;
    }
}
