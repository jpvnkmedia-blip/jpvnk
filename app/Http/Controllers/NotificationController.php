<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Paparan Pusat Notifikasi & Aktiviti Pengguna
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->userNotifications()->latest();

        // Tapisan Status (unread / read / all)
        $status = $request->query('status', 'all');
        if ($status === 'unread') {
            $query->unread();
        } elseif ($status === 'read') {
            $query->read();
        }

        // Tapisan Kategori / Modul (type)
        $type = $request->query('type');
        if ($type && $type !== 'all') {
            if ($type === 'eptr_pawah') {
                $query->whereIn('type', ['eptr', 'pawah']);
            } elseif ($type === 'stor_kenderaan') {
                $query->whereIn('type', ['inventori', 'kenderaan']);
            } elseif ($type === 'keselamatan') {
                $query->whereIn('type', ['auth', 'profil', 'sistem']);
            } else {
                $query->where('type', $type);
            }
        }

        // Carian teks
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $notifications = $query->paginate(15)->withQueryString();

        // Statistik
        $totalCount = $user->userNotifications()->count();
        $unreadCount = $user->userNotifications()->unread()->count();
        $eptrCount = $user->userNotifications()->whereIn('type', ['eptr', 'pawah'])->count();
        $epuCount = $user->userNotifications()->where('type', 'epu')->count();
        $kursusCount = $user->userNotifications()->where('type', 'kursus')->count();
        $klinikCount = $user->userNotifications()->where('type', 'klinik')->count();
        $storCount = $user->userNotifications()->whereIn('type', ['inventori', 'kenderaan'])->count();
        $securityCount = $user->userNotifications()->whereIn('type', ['auth', 'profil', 'sistem'])->count();

        return view('notifications.index', compact(
            'notifications',
            'status',
            'type',
            'totalCount',
            'unreadCount',
            'eptrCount',
            'epuCount',
            'kursusCount',
            'klinikCount',
            'storCount',
            'securityCount'
        ));
    }

    /**
     * Dapatkan notifikasi terkini & kiraan unread (untuk AJAX dropdown navbar)
     */
    public function feed(Request $request)
    {
        $user = Auth::user();
        $unreadCount = $user->unreadNotificationsCount();
        $recent = $user->userNotifications()
            ->latest()
            ->limit(8)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'title' => $item->title,
                    'message' => $item->message,
                    'action_url' => $item->action_url,
                    'icon' => $item->icon,
                    'color' => $item->color,
                    'is_read' => $item->isRead(),
                    'time_ago' => $item->created_at->diffForHumans(),
                    'formatted_date' => $item->created_at->format('d/m/Y h:i A'),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $recent,
        ]);
    }

    /**
     * Tandakan satu notifikasi sebagai telah dibaca
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->userNotifications()->findOrFail($id);
        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotificationsCount(),
            ]);
        }

        if ($request->has('redirect') && !empty($notification->action_url)) {
            return redirect($notification->action_url);
        }

        return back()->with('success', 'Notifikasi ditandakan sebagai telah dibaca.');
    }

    /**
     * Tandakan semua notifikasi pengguna sebagai telah dibaca
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $user->userNotifications()->unread()->update(['read_at' => Carbon::now()]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandakan sebagai dibaca.');
    }

    /**
     * Padam satu notifikasi
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->userNotifications()->findOrFail($id);
        $notification->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotificationsCount(),
            ]);
        }

        return back()->with('success', 'Notifikasi telah berjaya dipadam.');
    }

    /**
     * Padam semua notifikasi yang telah dibaca
     */
    public function clearRead(Request $request)
    {
        $user = Auth::user();
        $user->userNotifications()->read()->delete();

        return back()->with('success', 'Semua notifikasi yang telah dibaca telah dibersihkan.');
    }
}