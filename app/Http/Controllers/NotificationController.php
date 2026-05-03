<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display all notifications page (Web View).
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = auth()->user();

        // If API/AJAX request, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return $this->getNotificationsJson();
        }

        // Web view - paginated
        $notifications = $user->notifications()
            ->latest()
            ->paginate(15);

        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get notifications as JSON (for AJAX dropdown).
     */
    public function getNotificationsJson(): JsonResponse
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'general',
                    'title' => $notification->data['title'] ?? null,
                    'message' => $notification->data['message'] ?? 'You have a new notification.',
                    'url' => $notification->data['url'] ?? '#',
                    'notification_type' => $notification->data['notification_type'] ?? 'info',
                    'icon' => $notification->data['icon'] ?? 'fa-bell',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read and redirect to the action URL.
     * This is the primary click action for notification items.
     */
    public function read(string $id): RedirectResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->find($id);

        if (!$notification) {
            abort(404, 'Notification not found');
        }

        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('dashboard');

        return redirect($url);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->find($id);

        if (!$notification) {
            return response()->json(['error' => 'Notification not found.'], 404);
        }

        $notification->markAsRead();

        // If came from redirect, go back
        if ($request->has('redirect')) {
            return response()->json([
                'success' => true,
                'redirect' => $notification->data['url'] ?? url()->previous(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->find($id);

        if (!$notification) {
            return response()->json(['error' => 'Notification not found.'], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted.',
        ]);
    }

    /**
     * Clear all read notifications.
     */
    public function clearRead(): JsonResponse
    {
        $user = auth()->user();
        $user->readNotifications()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Read notifications cleared.',
        ]);
    }
}
