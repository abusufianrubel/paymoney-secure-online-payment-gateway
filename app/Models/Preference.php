<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Preference extends Model
{
	protected $table = 'preferences';
    protected $fillable = ['category', 'field', 'value'];
    public $timestamps = false;

    /*FOR CACHE - BELOW*/
    public static function getAll()
    {
        $cacheKey = config('cache.prefix') . '-preferences';

        try {
            $data = Cache::get($cacheKey);
        } catch (\Throwable $e) {
            return parent::all();
        }

        if (empty($data)) {
            $data = parent::all();

            try {
                Cache::put($cacheKey, $data, 30 * 86400);
            } catch (\Throwable $e) {
                // Cache store unavailable (e.g. missing cache table during upgrade)
            }
        }

        return $data;
    }
}
