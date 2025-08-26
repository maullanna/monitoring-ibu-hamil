<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BackupLog;

class BackupController extends Controller
{
    public function index()
    {
        return view('admin.backup');
    }

    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string',
        ]);

        // Simulasi backup (nanti bisa diimplementasikan dengan backup database real)
        BackupLog::create([
            'admin_id' => auth()->id(),
            'deskripsi' => $request->deskripsi,
            'status' => 'Sukses',
        ]);

        return redirect()->route('admin.backup')->with('success', 'Backup berhasil dibuat!');
    }
}
