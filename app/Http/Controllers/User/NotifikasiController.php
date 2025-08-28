<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    /**
     * Tampilkan semua notifikasi user
     */
    public function index()
    {
        $notifications = Auth::user()->notifikasi()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('user.notifikasi', compact('notifications'));
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifikasi()->findOrFail($id);
        $notification->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        Auth::user()->notifikasi()
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return response()->json(['success' => true]);
    }

    /**
     * Hapus notifikasi
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifikasi()->findOrFail($id);
        $notification->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * API untuk mendapatkan notifikasi unread count
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->notifikasi()
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }

    /**
     * API untuk mendapatkan notifikasi terbaru
     */
    public function getLatestNotifications()
    {
        $notifications = Auth::user()->notifikasi()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return response()->json($notifications);
    }
}
