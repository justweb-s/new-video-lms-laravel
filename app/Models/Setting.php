<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';
    public $timestamps = true;

    protected $fillable = [
        'key', 'value'
    ];

    // Retrieve a setting value by key with optional default
    public static function get(string $key, $default = null)
    {
        $record = static::query()->where('key', $key)->first();
        return $record ? $record->value : $default;
    }

    // Set or update a setting value
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
