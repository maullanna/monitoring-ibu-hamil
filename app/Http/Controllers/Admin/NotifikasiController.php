<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index()
    {
        return view('admin.notifikasi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
        ]);

        Notifikasi::create([
            'user_id' => $request->user_id,
            'judul' => $request->judul,
            'pesan' => $request->pesan,
            'is_read' => false,
        ]);

        return redirect()->route('admin.notifikasi')->with('success', 'Notifikasi berhasil dikirim!');
    }
}
