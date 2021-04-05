<?php

namespace App\Traits;

use App\Models\Absence;
use App\Models\Location;
use App\Models\AbsenceType;
use App\Models\AbsenceIndex;
use App\Models\AbsenceTypeUser;

trait HasAbsences
{

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function absenceTypes()
    {
        return $this->belongsToMany(AbsenceType::class, AbsenceTypeUser::class)
            ->withTimestamps()
            ->as('absenceTypes');
    }

    public function absenceIndex()
    {
        return $this->hasMany(AbsenceIndex::class);
    }

    public function absencesForLocation(Location $location)
    {
        return $this->absences()->where('location_id', $location->id)->get();
    }

    public function absenceTypesForLocation(Location $location)
    {
        return $this->absenceTypes->filter(function (AbsenceType $absenceType) use ($location) {
            return $absenceType->location_id === $location->id;
        })->pluck('title','id');
    }
}
