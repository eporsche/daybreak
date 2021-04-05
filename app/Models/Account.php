<?php

namespace App\Models;

use App\Models\Location;
use App\Daybreak;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;

    protected $casts = [
        'owned_by' => 'integer'
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owned_by');
    }

    public function purge()
    {
        if (Daybreak::hasProjectBillingFeature()) {
            $this->projects->each->purge();
        }

        //delete assigned locations
        $this->locations->each->purge();

        //delete ownership
        $this->owner->forceFill([
            'account_id' => null
        ])->save();

        //delete owned account
        $this->delete();
    }
}
