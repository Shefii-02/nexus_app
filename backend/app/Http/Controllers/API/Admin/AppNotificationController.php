<?php

namespace App\Http\Controllers\API\Admin;
use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppNotificationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            AppNotification::where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->paginate($request->integer('per_page', 20))
        );
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => AppNotification::where('user_id', auth()->id())
                ->whereNull('read_at')->count(),
        ]);
    }

    public function markRead(int $id)
    {
        $n = AppNotification::where('user_id', auth()->id())->findOrFail($id);
        $n->read_at ??= now();
        $n->save();
        return response()->json(['data' => $n]);
    }

    public function markAllRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['status' => 'ok']);
    }
}
