<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MonitoringDehidrasi;

class MonitoringController extends Controller
{
    public function index()
    {
        return view('user.monitoring');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah_minum_ml' => 'required|integer|min:0|max:5000',
        ]);

        // Check if already exists for today
        $existing = MonitoringDehidrasi::where('pasien_id', Auth::user()->pasien->id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return redirect()->route('user.monitoring')->with('error', 'Data untuk tanggal ini sudah ada!');
        }

        // Determine status based on target
        $target = Auth::user()->pasien->target_minum_ml ?? 2000;
        $status = null;
        
        if ($request->jumlah_minum_ml < ($target * 0.8)) {
            $status = 'Kurang';
        } elseif ($request->jumlah_minum_ml >= $target) {
            $status = 'Cukup';
        } else {
            $status = 'Berlebihan';
        }

        MonitoringDehidrasi::create([
            'pasien_id' => Auth::user()->pasien->id,
            'tanggal' => $request->tanggal,
            'jumlah_minum_ml' => $request->jumlah_minum_ml,
            'status' => $status,
        ]);

        return redirect()->route('user.monitoring')->with('success', 'Data monitoring berhasil disimpan!');
    }
}
