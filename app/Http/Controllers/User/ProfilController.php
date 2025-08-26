<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function index()
    {
        return view('user.profil');
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'usia_kehamilan_minggu' => 'nullable|integer|min:1|max:42',
            'target_minum_ml' => 'nullable|integer|min:1000|max:5000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Update user data
            Auth::user()->update([
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
            ]);

            // Handle foto upload
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                // Delete old foto if exists
                if (Auth::user()->pasien->foto) {
                    \Storage::disk('public')->delete(Auth::user()->pasien->foto);
                }
                
                // Store new foto
                $fotoPath = $request->file('foto')->store('pasien-foto', 'public');
            }

            // Update pasien data
            Auth::user()->pasien->update([
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'usia_kehamilan_minggu' => $request->usia_kehamilan_minggu,
                'target_minum_ml' => $request->target_minum_ml,
                'foto' => $fotoPath ?: Auth::user()->pasien->foto,
            ]);

            return redirect()->route('user.profil')->with('success', 'Profil berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->route('user.profil')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
