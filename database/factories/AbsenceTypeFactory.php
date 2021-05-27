<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\AbsenceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsenceTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AbsenceType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'location_id' => Location::factory(),
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ];
    }
}
