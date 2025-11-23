<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $guarded = [

    ];

    public static function set($userId, $key, $value)
{
    return UserSetting::updateOrCreate(
        ['user_id' => $userId, 'key' => $key],
        ['value' => $value]
    );
}


public static function get($userId, $key, $default = null)
{
    return UserSetting::where('user_id', $userId)
                      ->where('key', $key)
                      ->value('value') ?? $default;
}

}
