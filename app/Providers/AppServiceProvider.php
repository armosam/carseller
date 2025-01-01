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

    }
}
