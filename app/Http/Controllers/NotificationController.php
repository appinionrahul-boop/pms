<?php

namespace App\Http\Controllers;

use App\Models\Notificaton;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Return last 100 notifications as a small HTML list (for the modal body)
    public function list()
    {
        $items = Notificaton::orderByDesc('created_at')->limit(100)->get();

        return view('notifications._list', compact('items')); // partial
    }

    // Mark all unseen as seen
    public function markAllSeen(Request $request)
    {
        Notificaton::where('is_seen', false)->update(['is_seen' => true]);
        $unseen = 0;
        return response()->json(['ok' => true, 'unseen' => $unseen]);
    }
}
