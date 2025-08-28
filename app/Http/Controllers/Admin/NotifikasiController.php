<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use Carbon\Carbon;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Notifikasi::with('user');
        
        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by type
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        
        // Filter by priority
        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $notifikasiData = $query->latest()->get();
        
        return view('admin.notifikasi', compact('notifikasiData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
            'tipe' => 'required|in:info,warning,success,danger',
            'prioritas' => 'required|in:low,normal,high,urgent',
        ]);

        Notifikasi::create([
            'user_id' => $request->user_id,
            'judul' => $request->judul,
            'pesan' => $request->pesan,
            'tipe' => $request->tipe,
            'prioritas' => $request->prioritas,
            'is_read' => false,
        ]);

        return redirect()->route('admin.notifikasi')->with('success', 'Notifikasi berhasil dikirim!');
    }
}
