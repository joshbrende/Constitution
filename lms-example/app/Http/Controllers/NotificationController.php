<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark one as read and redirect to its action_url.
     */
    public function readAndGo(string $id)
    {
        $user = Auth::user();
        $n = $user->notifications()->findOrFail($id);
        $n->markAsRead();
        $url = $n->data['action_url'] ?? route('notifications.index');

        return redirect($url);
    }

    /**
     * Mark all as read.
     */
    public function markAllRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->route('notifications.index')->with('message', 'All notifications marked as read.');
    }
}
