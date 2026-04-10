<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SupportQuestionSubmitted;
use App\Models\SupportQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminFaqController extends Controller
{
    private const SUPPORT_EMAIL = 'support@ttm-group.co.za';

    public function index(): View
    {
        $faqs = [
            [
                'q' => 'How do I grant or revoke a certificate?',
                'a' => 'Go to Administration → Certificates. You can revoke or reinstate certificates there. All actions are recorded for audit.',
            ],
            [
                'q' => 'Why can’t I see a section like Academy or Library in Administration?',
                'a' => 'Your access depends on assigned admin roles and allowed sections. Ask a System Administrator to review your role permissions.',
            ],
            [
                'q' => 'How does membership get granted?',
                'a' => 'Membership is granted when a user passes the membership course assessment (when configured as membership-granting). A certificate is then issued.',
            ],
            [
                'q' => 'How do I moderate Dialogue threads?',
                'a' => 'Go to Administration → Dialogue. You can view threads, pin/remove messages, and lock/unlock threads depending on your permissions.',
            ],
            [
                'q' => 'Where do I edit Terms / Privacy / Cookies content?',
                'a' => 'Go to Administration → Help & legal pages. Edit the Static Pages for terms, privacy, or cookies, then ensure they are published.',
            ],
        ];

        return view('admin.guide.faq', [
            'faqs' => $faqs,
            'supportEmail' => self::SUPPORT_EMAIL,
        ]);
    }

    public function storeQuestion(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $q = SupportQuestion::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'source' => 'admin_faq',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Email developers (best-effort; DB is the source of truth).
        try {
            Mail::to(self::SUPPORT_EMAIL)->send(new SupportQuestionSubmitted($q));
        } catch (\Throwable $_e) {
            // Ignore email transport errors; message is stored in DB.
        }

        return redirect()
            ->route('admin.guide.faq')
            ->with('success', 'Thank you. Your question was sent to the developers.');
    }
}

