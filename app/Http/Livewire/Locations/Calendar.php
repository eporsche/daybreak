<?php

namespace App\Http\Livewire\Locations;

use Carbon\Carbon;
use App\Models\AbsenceIndex;
use App\Models\PublicHoliday;
use Illuminate\Support\Collection;
use Asantibanez\LivewireCalendar\LivewireCalendar;

class Calendar extends LivewireCalendar
{
    public $employee;

    public function mount(
        $initialYear = null,
        $initialMonth = null,
        $weekStartsAt = null,
        $calendarView = null,
        $dayView = null,
        $eventView = null,
        $dayOfWeekView = null,
        $dragAndDropClasses = null,
        $beforeCalendarView = "calendars.before-calendar-view",
        $afterCalendarView = null,
        $pollMillis = null,
        $pollAction = null,
        $dragAndDropEnabled = false,
        $dayClickEnabled = false,
        $eventClickEnabled = false,
        $extras = [],
        $employee = null
    ) {

        $this->employee = $employee;

        parent::mount(
            $initialYear,
            $initialMonth,
            $weekStartsAt,
            $calendarView,
            $dayView,
            $eventView,
            $dayOfWeekView,
            $dragAndDropClasses,
            $beforeCalendarView,
            $afterCalendarView,
            $pollMillis,
            $pollAction,
            $dragAndDropEnabled,
            $dayClickEnabled,
            $eventClickEnabled,
            $extras
        );
    }

    public function events() : Collection
    {
        $absences = AbsenceIndex::where('location_id', $this->employee->currentLocation->id)
            ->get()
            ->map(function (AbsenceIndex $model) {
                return [
                    'id' => $model->id,
                    'title' => $model->absenceType->title,
                    'description' => $model->user->name,
                    'date' => $model->date,
                ];
            });

        $holidays = PublicHoliday::where('location_id', $this->employee->currentLocation->id)
            ->get()
            ->map(function (PublicHoliday $model) {
                return [
                    'id' => $model->id,
                    'title' => __("Public Holiday"),
                    'description' => $model->title,
                    'date' => $model->day,
                ];
            });

        return collect()->merge($absences)->merge($holidays);
    }
}
