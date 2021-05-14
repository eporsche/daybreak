<?php

namespace App;

use App\Providers\Features;
use App\Contracts\AddsAbsences;
use App\Contracts\AddsLocation;
use App\Formatter\DateFormatter;
use App\Contracts\RemovesAbsence;
use App\Contracts\AddsAbsenceType;
use App\Contracts\AddsTargetHours;
use App\Contracts\ApprovesAbsence;
use App\Contracts\DeletesAccounts;
use App\Contracts\RemovesLocation;
use App\Contracts\DeletesLocations;
use App\Contracts\AddsPublicHoliday;
use App\Contracts\AddsTimeTrackings;
use App\Contracts\FiltersEvaluation;
use App\Contracts\RemovesTargetHour;
use App\Contracts\RemovesAbsenceType;
use App\Contracts\UpdatesAbsenceType;
use App\Contracts\AddsLocationMembers;
use App\Contracts\RemovesTimeTracking;
use App\Contracts\UpdatesTimeTracking;
use App\Contracts\RemovesPublicHoliday;
use App\Contracts\UpdatesLocationNames;
use App\Contracts\ImportsPublicHolidays;
use App\Contracts\AddsDefaultRestingTime;
use App\Contracts\InvitesLocationMembers;
use App\Contracts\RemovesLocationMembers;
use App\Contracts\UpdatesEmployeeProfile;
use App\Contracts\AddsVacationEntitlements;
use App\Contracts\UpdatesLocationMembersRole;
use App\Contracts\RemovesVacationEntitlements;
use App\Contracts\TransfersVacationEntitlements;

class Daybreak
{
    public static $membershipModel = 'App\\Models\\Membership';

    public static $locationModel = 'App\\Models\\Location';

    public static function membershipModel()
    {
        return static::$membershipModel;
    }

    public static function locationModel()
    {
        return static::$locationModel;
    }

    /**
     * Get a new instance of the location model.
     *
     * @return mixed
     */
    public static function newLocationModel()
    {
        $model = static::locationModel();

        return new $model;
    }

    /**
     * Specify the location model that should be used by Daybreak.
     *
     * @param  string  $model
     * @return static
     */
    public static function useLocationModel(string $model)
    {
        static::$locationModel = $model;

        return new static;
    }

    public static function inviteLocationMembersUsing(string $class)
    {
        return app()->singleton(InvitesLocationMembers::class, $class);
    }

    public static function addLocationMembersUsing(string $class)
    {
        return app()->singleton(AddsLocationMembers::class, $class);
    }

    /**
     * Register a class / callback that should be used to update location names.
     *
     * @param  string  $class
     * @return void
     */
    public static function updateLocationNamesUsing(string $class)
    {
        return app()->singleton(UpdatesLocationNames::class, $class);
    }

    public static function updatesLocationMembersRoleUsing(string $class)
    {
        return app()->singleton(UpdatesLocationMembersRole::class, $class);
    }

    public static function removesLocationMembersUsing(string $class)
    {
        return app()->singleton(RemovesLocationMembers::class, $class);
    }

    public static function addsTargetHoursUsing(string $class)
    {
        return app()->singleton(AddsTargetHours::class, $class);
    }

    public static function addsAbsencesUsing(string $class)
    {
        return app()->singleton(AddsAbsences::class, $class);
    }

    public static function formatsDatesUsing(string $class)
    {
        return app()->singleton(DateFormatter::class, $class);
    }

    public static function addsTimeTrackingsUsing(string $class)
    {
        return app()->singleton(AddsTimeTrackings::class, $class);
    }

    public static function approvesAbsenceUsing(string $class)
    {
        return app()->singleton(ApprovesAbsence::class, $class);
    }

    public static function addsPublicHolidayUsing(string $class)
    {
        return app()->singleton(AddsPublicHoliday::class, $class);
    }

    public static function removesPublicHolidayUsing(string $class)
    {
        return app()->singleton(RemovesPublicHoliday::class, $class);
    }

    public static function filtersEvaluationUsing(string $class)
    {
        return app()->singleton(FiltersEvaluation::class, $class);
    }

    public static function removesTimeTrackingUsing(string $class)
    {
        return app()->singleton(RemovesTimeTracking::class, $class);
    }

    public static function removesAbsenceUsing(string $class)
    {
        return app()->singleton(RemovesAbsence::class, $class);
    }

    public static function removesAbsenceTypeUsing(string $class)
    {
        return app()->singleton(RemovesAbsenceType::class, $class);
    }

    public static function addsAbsenceTypeUsing(string $class)
    {
        return app()->singleton(AddsAbsenceType::class, $class);
    }

    public static function removesTargetHourUsing(string $class)
    {
        return app()->singleton(RemovesTargetHour::class, $class);
    }

    public static function addsVacationEntitlementUsing(string $class)
    {
        return app()->singleton(AddsVacationEntitlements::class, $class);
    }

    public static function transfersVacationEntitlementUsing(string $class)
    {
        return app()->singleton(TransfersVacationEntitlements::class, $class);
    }

    public static function removesVacationEntitlementUsing(string $class)
    {
        return app()->singleton(RemovesVacationEntitlements::class, $class);
    }

    public static function importsPublicHolidaysUsing(string $class)
    {
        return app()->singleton(ImportsPublicHolidays::class, $class);
    }

    public static function deletesAccountsUsing(string $class)
    {
        return app()->singleton(DeletesAccounts::class, $class);
    }

    public static function deletesLocationsUsing(string $class)
    {
        return app()->singleton(DeletesLocations::class, $class);
    }

    public static function removesLocationsUsing(string $class)
    {
        return app()->singleton(RemovesLocation::class, $class);
    }

    public static function addsLocationsUsing(string $class)
    {
        return app()->singleton(AddsLocation::class, $class);
    }

    public static function updatesTimeTrackingsUsing(string $class)
    {
        return app()->singleton(UpdatesTimeTracking::class, $class);
    }

    public static function updatesAbsenceTypeUsing(string $class)
    {
        return app()->singleton(UpdatesAbsenceType::class, $class);
    }

    public static function updatesEmployeeProfileUsing(string $class)
    {
        return app()->singleton(UpdatesEmployeeProfile::class, $class);
    }

    public static function addsDefaultRestingTimeUsing(string $class)
    {
        return app()->singleton(AddsDefaultRestingTime::class, $class);
    }

    /**
     * Determine if the application has payroll enabled.
     *
     * @return bool
     */
    public static function hasEmployeePayrollFeature()
    {
        return Features::hasEmployeePayrollFeature();
    }

    /**
     * Determine if the application has payroll enabled.
     *
     * @return bool
     */
    public static function hasProjectBillingFeature()
    {
        return Features::hasProjectBillingFeature();
    }

    /**
     * Determine if the application has payroll enabled.
     *
     * @return bool
     */
    public static function hasCaldavFeature()
    {
        return Features::hasCaldavFeature();
    }
}
