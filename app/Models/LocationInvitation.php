<?php

namespace App\Models;

use App\Models\Location;
use Illuminate\Database\Eloquent\Model;

class LocationInvitation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'role',
    ];

    /**
     * Get the team that the invitation belongs to.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
