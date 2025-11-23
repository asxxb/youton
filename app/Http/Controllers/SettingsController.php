<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Fetch settings as key/value array
        $settings = UserSetting::where('user_id', $userId)
                               ->pluck('value', 'key')
                               ->toArray();

        return view('settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();

        foreach ($request->except('_token') as $key => $value) {
            UserSetting::updateOrCreate(
                ['user_id' => $userId, 'key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Settings updated!');
    }
}
