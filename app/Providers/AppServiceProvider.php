<?php

namespace App\Providers;

use App\Daybreak;
use Carbon\Carbon;
use Livewire\Livewire;
use Brick\Math\BigDecimal;
use App\Actions\AddAbsence;
use Illuminate\Support\Arr;
use App\Actions\AddLocation;
use App\Actions\AddTargetHour;
use App\Actions\DeleteAccount;
use App\Actions\RemoveAbsence;
use App\Actions\AddAbsenceType;
use App\Actions\DeleteLocation;
use App\Actions\RemoveLocation;
use App\Actions\AddTimeTracking;
use App\Actions\ApproveAbscence;
use App\Actions\AddPublicHoliday;
use App\Actions\FilterEvaluation;
use App\Actions\RemoveTargetHour;
use App\Actions\AddLocationMember;
use App\Actions\RemoveAbsenceType;
use App\Actions\UpdateAbsenceType;
use Illuminate\Support\Collection;
use App\Actions\RemoveTimeTracking;
use App\Actions\UpdateLocationName;
use App\Actions\UpdateTimeTracking;
use App\Actions\RemovePublicHoliday;
use App\Actions\ImportPublicHolidays;
use App\Actions\InviteLocationMember;
use App\Actions\RemoveLocationMember;
use Illuminate\Support\Facades\Blade;
use App\Actions\UpdateEmployeeProfile;
use App\Formatter\GermanDateFormatter;
use App\Actions\AddVacationEntitlement;
use Illuminate\Support\ServiceProvider;
use App\Actions\UpdateLocationMemberRole;
use App\Http\Livewire\Locations\Calendar;
use Illuminate\Database\Eloquent\Builder;
use App\Actions\RemoveVacationEntitlement;
use Carbon\Exceptions\InvalidTypeException;
use App\Actions\TransferVacationEntitlement;
use Illuminate\View\Compilers\BladeCompiler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLivewireComponents();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setlocale(config('app.locale'));

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'daybreak');
        $this->configureComponents();

        $this->registerCollectionMacros();
        $this->registerBuilderMacros();

        Daybreak::addLocationMembersUsing(AddLocationMember::class);
        Daybreak::updateLocationNamesUsing(UpdateLocationName::class);
        Daybreak::inviteLocationMembersUsing(InviteLocationMember::class);
        Daybreak::updatesLocationMembersRoleUsing(UpdateLocationMemberRole::class);
        Daybreak::removesLocationMembersUsing(RemoveLocationMember::class);
        Daybreak::addsTargetHoursUsing(AddTargetHour::class);
        Daybreak::addsAbsencesUsing(AddAbsence::class);
        Daybreak::formatsDatesUsing(GermanDateFormatter::class);
        Daybreak::addsTimeTrackingsUsing(AddTimeTracking::class);
        Daybreak::approvesAbsenceUsing(ApproveAbscence::class);
        Daybreak::filtersEvaluationUsing(FilterEvaluation::class);
        Daybreak::removesTimeTrackingUsing(RemoveTimeTracking::class);
        Daybreak::removesAbsenceUsing(RemoveAbsence::class);
        Daybreak::removesAbsenceTypeUsing(RemoveAbsenceType::class);
        Daybreak::addsAbsenceTypeUsing(AddAbsenceType::class);
        Daybreak::removesTargetHourUsing(RemoveTargetHour::class);
        Daybreak::addsVacationEntitlementUsing(AddVacationEntitlement::class);
        Daybreak::transfersVacationEntitlementUsing(TransferVacationEntitlement::class);
        Daybreak::removesVacationEntitlementUsing(RemoveVacationEntitlement::class);
        Daybreak::deletesAccountsUsing(DeleteAccount::class);
        Daybreak::deletesLocationsUsing(DeleteLocation::class);
        Daybreak::removesLocationsUsing(RemoveLocation::class);
        Daybreak::addsLocationsUsing(AddLocation::class);
        Daybreak::updatesTimeTrackingsUsing(UpdateTimeTracking::class);
        Daybreak::updatesAbsenceTypeUsing(UpdateAbsenceType::class);
        Daybreak::updatesEmployeeProfileUsing(UpdateEmployeeProfile::class);
        Daybreak::importsPublicHolidaysUsing(ImportPublicHolidays::class);
        Daybreak::addsPublicHolidayUsing(AddPublicHoliday::class);
        Daybreak::removesPublicHolidayUsing(RemovePublicHoliday::class);

        Collection::macro('mapToMultipleSelect', function () {
            /**
             * @var Collection $this
             */
            return $this->map(
                function ($value, $key) {
                    return [
                    'id' => $key,
                    'title' => $value,
                    ];
                })->values()->toArray();
        });

        Collection::macro('filterMultipleSelect', function ($callback) {
            /**
             * @var Collection $this
             */
            return $this->filter(function ($item) use ($callback) {
                return call_user_func($callback, $item);
            })->values()->toArray();
        });
    }

    /**
     * Configure the Daybreak Blade components.
     *
     * @return void
     */
    protected function configureComponents()
    {
        $this->callAfterResolving(BladeCompiler::class, function () {
            $this->registerComponent('locations.switchable-location');
        });
    }

    public function registerLivewireComponents()
    {
        Livewire::component('location-calendar', Calendar::class);
    }

    /**
     * Register the given component.
     *
     * @param  string  $component
     * @return void
     */
    protected function registerComponent(string $component)
    {
        Blade::component('daybreak::'.$component, 'daybreak-'.
            str_replace('.','-',$component)
        );
    }

    public function registerBuilderMacros()
    {
        Builder::macro('whereLike', function ($attributes, string $searchTerm = null) {
            if (!$searchTerm) {
                return $this;
            }

            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });

            return $this;
        });
    }


    public function registerCollectionMacros()
    {
        Collection::macro('sumBigDecimals', function ($callback = null) {
            /**
             * @var Collection $this
             */
            $callback = is_null($callback)
                ? $this->identity()
                : $this->valueRetriever($callback);

            return $this->reduce(
                function (BigDecimal $carry, $item) use ($callback) {
                    $value = $callback($item);
                    if (!($value instanceof BigDecimal)) {
                        throw new InvalidTypeException("Passed value should be of type BigDecimal");
                    }

                    return $carry->plus($value);
                },
                BigDecimal::zero()
            );
        });
    }
}
