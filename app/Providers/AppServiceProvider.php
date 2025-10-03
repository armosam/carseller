<?php

namespace App\Providers;

//use App\Models\Model;
use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // If you need to prevent lazy loading on the whole project
        // Then you need to use with() everywhere (eager loading)
        //Model::preventLazyLoading();

        // Register pagination
        Paginator::defaultView('pagination');

        // Share a year in the all views
        View::share('year', date('Y'));

        // Listen to all database queries and log in the file logs/query.log
        if (app()->environment('local') && env('LOG_SLOW_QUERY', false)) {
            DB::listen(function ($query) {
                if ($query->time > env('LOG_SLOW_QUERY_TIMEOUT', 10)) {
                    Log::channel('sql')->debug('Slow Query', [
                        'query' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                    ]);
                }
            });
        }

        // Configure default password rules
        Password::defaults(function () {
            $rule = Password::min(8);

            return $this->app->isProduction()
                ? $rule->numbers()->symbols()->mixedCase()->uncompromised()
                : $rule;
        });

        // Customize the password reset URL when sending email
        /*ResetPassword::createUrlUsing(function ($notifiable, $token) {
            // Custom URL format for password reset in the email
            return url("auth/set-password/$token?email=" . urlencode($notifiable->getEmailForPasswordReset()));
        });*/


        // GATES
        /*Gate::before(function (User $user, string $ability) {
            // Define what happens before gates
            // Returning true skips all gates and gives access
            // Returning false skips all gates and denys access
            // Returning null will go to next defined gate checking

           if ($user->can('admin')) {
               return true;
           }
           if ($user->can('guest')) {
               return false;
           }
           return null;
        });

        Gate::after(function (User $user, string $ability) {
           // Define what should be done after each gate
        });*/

        // Car Update authorization rule as Gate
        /*Gate::define('car_update', function (User $user, Car $car) {
            return $car->owner()->is($user);
        });*/

        // Gate with custom deny message
        /*Gate::define('car_destroy', function (User $user, Car $car) {
            return $car->owner()->is($user)
                ? \Illuminate\Auth\Access\Response::allow()
                : \Illuminate\Auth\Access\Response::deny('Sorry you cannot delete this car.');
        });*/

    }
}
