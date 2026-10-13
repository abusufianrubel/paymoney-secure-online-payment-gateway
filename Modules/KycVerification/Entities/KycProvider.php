<?php

namespace Modules\KycVerification\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KycProvider extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'alias', 'is_default'];

    /**
     * Update provider name
     * @param \illuminate\Http\Request $request
     * @param KycProvider $provider
     * @return void
     */
    public function updateProvider($request, $provider)
    {
        $provider->name = $request->name;
        $provider->is_default = $request->is_default;
        $provider->save();
        
        if ($request->is_default == 'Yes') {
            $this->where('id', '!=', $provider->id)->update(['is_default' => 'No']);
        }
    }
}
