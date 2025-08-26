<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Pasien;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            Log::info('User login successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'is_admin' => $user->isAdmin()
            ]);

            if ($user->isAdmin()) {
                Log::info('Redirecting admin to admin dashboard');
                return redirect()->intended('/admin/dashboard');
            } else {
                Log::info('Redirecting user to user dashboard');
                return redirect()->intended('/user/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    private function generateQRCode($user)
    {
        $qrData = json_encode([
            'id' => $user->id,
            'nama' => $user->nama_lengkap,
            'email' => $user->email,
        ]);

        try {
            // Use SVG format directly to avoid imagick dependency
            $qrCode = QrCode::format('svg')->size(300)->generate($qrData);
            $qrPath = 'qr_codes/' . $user->id . '.svg';
            Storage::disk('public')->put($qrPath, $qrCode);
            return $qrPath;
        } catch (\Exception $e) {
            Log::error('SVG QR generation failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Start transaction
            DB::beginTransaction();

            $user = User::create([
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
            ]);

            Log::info('User created successfully', ['user_id' => $user->id]);

            // Generate QR Code
            $qrPath = $this->generateQRCode($user);
            if ($qrPath) {
                $user->update(['qr_code' => $qrPath]);
                Log::info('QR code generated', ['qr_path' => $qrPath]);
            }

            // Create pasien record
            $pasien = Pasien::create([
                'user_id' => $user->id,
                'target_minum_ml' => 2000,
            ]);

            Log::info('Pasien record created', ['pasien_id' => $pasien->id, 'user_id' => $user->id]);

            // Commit transaction
            DB::commit();

            Auth::login($user);

            Log::info('User registration completed successfully', ['user_id' => $user->id]);

            return redirect('/user/dashboard');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();

            Log::error('User registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'email' => 'Terjadi kesalahan saat registrasi: ' . $e->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
