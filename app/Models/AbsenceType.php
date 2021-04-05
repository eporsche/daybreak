<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenceType extends Model
{
    use HasFactory;

    protected $casts = [
        'location_id' => 'integer',
        'affect_evaluations' => 'boolean',
        'affect_vacation_times' => 'boolean'
    ];

    protected $fillable = [
        'user_id',
        'title',
        'affect_evaluations',
        'affect_vacation_times'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, AbsenceTypeUser::class)
            ->withTimestamps()
            ->as('users');
    }

    public function shouldSumAbsenceHoursUpToTargetHours()
    {
        return  $this->evaluation_calculation_setting === 'absent_to_target';
    }

    public function affectsVacation()
    {
        return  $this->affect_vacation_times;
    }

    public function affectsEvaluation()
    {
        return $this->affect_evaluations;
    }
}
