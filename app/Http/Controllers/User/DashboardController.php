<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        Log::info('User dashboard accessed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'is_admin' => $user->isAdmin()
        ]);

        return view('user.dashboard');
    }
}
