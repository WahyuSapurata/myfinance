<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    public static function getValue($key, $default = null)
    {
        return static::where('user_id', auth()->id())
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public static function setValue($key, $value)
    {
        return static::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'key' => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }
}
