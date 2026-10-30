<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the mahasiswa dashboard.
     */
    public function __invoke(): View
    {
        return view('mahasiswa.dashboard');
    }
}
