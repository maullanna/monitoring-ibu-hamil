<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use App\Models\Pasien;
use App\Models\User;

class QrCodeController extends Controller
{
    /**
     * Generate QR Code untuk profil ibu hamil
     */
    public function generateProfileQrCode()
    {
        $user = Auth::user();
        $pasien = $user->pasien;

        if (!$pasien) {
            return response()->json(['error' => 'Data pasien tidak ditemukan'], 404);
        }

        // Data yang akan di-encode dalam QR code (URL ke profile public dengan NGROK)
        $ngrokUrl = 'https://8534a71bf115.ngrok-free.app'; // NGROK URL untuk demo external
        $qrData = $ngrokUrl . '/profile/' . $user->id;
        
        // Debug log untuk QR code data
        \Log::info('QR Code Data Generated', [
            'user_id' => $user->id,
            'qr_url' => $qrData,
            'url_length' => strlen($qrData)
        ]);

        // Generate QR Code dengan package endroid/qr-code
        $qrCode = new QrCode($qrData);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::High);

        // Create SVG writer
        $writer = new SvgWriter();
        $result = $writer->write($qrCode);

        // Return QR code sebagai response
        return response($result->getString())
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'inline; filename="profile-qr-code.svg"');
    }

    /**
     * Generate QR Code untuk download
     */
    public function downloadProfileQrCode()
    {
        $user = Auth::user();
        $pasien = $user->pasien;

        if (!$pasien) {
            return response()->json(['error' => 'Data pasien tidak ditemukan'], 404);
        }

        // Data yang akan di-encode dalam QR code (URL ke profile public dengan NGROK)
        $ngrokUrl = 'https://8534a71bf115.ngrok-free.app'; // NGROK URL untuk demo external
        $qrData = $ngrokUrl . '/profile/' . $user->id;

        // Generate QR Code dengan package endroid/qr-code
        $qrCode = new QrCode($qrData);
        $qrCode->setSize(400);
        $qrCode->setMargin(15);
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::High);

        // Create PNG writer for download
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Return QR code untuk download
        return response($result->getString())
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="profile-qr-code-' . $user->nama_lengkap . '.png"');
    }

    /**
     * Tampilkan profile public untuk QR code
     */
    public function showPublicProfile($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $pasien = $user->pasien;

            if (!$pasien) {
                abort(404, 'Data pasien tidak ditemukan');
            }

            return view('user.public-profile', compact('user', 'pasien'));
        } catch (\Exception $e) {
            abort(404, 'Profile tidak ditemukan');
        }
    }
}
