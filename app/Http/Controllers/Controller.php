<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\PengaturanAplikasi;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    public function __construct()
    {
        // Share pengaturan aplikasi to all views
        try {
            $pengaturan = PengaturanAplikasi::first();
            view()->share('pengaturan', $pengaturan);
        } catch (\Exception $e) {
            view()->share('pengaturan', null);
        }
    }
}
