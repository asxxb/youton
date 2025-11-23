<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
          $youtube = app(\App\Http\Controllers\YouTubeController::class)
          ->channelInfo();


              $user = auth()->user();



    return view('dashboard', compact('youtube', 'user'));
    }
}