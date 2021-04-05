<?php

namespace App\Providers;

class Features
{
    /**
     * Determine if the given feature is enabled.
     *
     * @param  string  $feature
     * @return bool
     */
    public static function enabled(string $feature)
    {
        return in_array($feature, config('app.features', []));
    }

    public static function projectBilling()
    {
        return 'billing';
    }

    public static function employeePayroll()
    {
        return 'payroll';
    }

    public static function caldav()
    {
        return 'caldav';
    }

    /**
     * Determine if the application has project billing enabled.
     *
     * @return bool
     */
    public static function hasProjectBillingFeature()
    {
        return static::enabled(static::projectBilling());
    }

    /**
     * Determine if the application has payroll enabled.
     *
     * @return bool
     */
    public static function hasEmployeePayrollFeature()
    {
        return static::enabled(static::employeePayroll());
    }

    public static function hasCaldavFeature()
    {
        return static::enabled(static::caldav());
    }

}
