<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return unread notifications for the authenticated user.
     * Replace this with your actual notification logic.
     */
    public function index()
    {
        return response()->json([
            'unread_count'  => 0,
            'notifications' => [],
        ]);
    }

    public function markAsRead(int $id)
    {
        // Mark notification as read
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        // Mark all notifications as read
        return response()->json(['success' => true]);
    }
}
