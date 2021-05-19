<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        // Jetstream::defaultApiTokenPermissions(['read']);
        Jetstream::role('admin', __('Location administrator'), [
            'addAbsence',
            'approveAbsence',
            'removeAbsence',
            'filterAbsences',
            'addDefaultRestingTime',
            'viewAnyTimeTracking',
            'filterTimeTracking',
            'assignProjects',
            'editLocations',
            'switchReportEmployee',
        ])->description(__('Location administrators can perform updates on a location.'));

        Jetstream::role('employee', __('Employee'), [
            'addAbsence',
            'removeAbsence'
        ])->description(__('Employees have can create new working hours.'));
    }
}
