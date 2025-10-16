<?php

namespace App\View\Components;

use App\Models\Maker;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class SelectMaker extends Component
{
    public Collection $makers;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        /*// Put into in cache in default store
        Cache::set('makers', 'test', 5);
        Cache::put('makers', 'test', 5);*/

        /*// If there is already count it will not do anything will return false
        Cache::add('count', '0', now()->addMinutes(5));
        // Increments existing in the cache value
        Cache::increment('count', 5);*/

        // Put into in cache in file cache
        // Cache::store('file')->set('makers', 'test', 5);

        /*// Get from cache
        $count = Cache::get('count');
        dump($count);

        if (Cache::has('makers')) {
            $makers = Cache::get('makers');
            dump($makers);
        };*/

        // Putting makers into the cache. Option #1
        /*if (Cache::has('makers')) {
            $this->makers = Cache::get('makers');
        } else {
            $this->makers = Maker::query()->orderBy('name')->get();
            Cache::put('makers', $this->makers, now()->addMinutes(1));
        }*/
        // or
        /*$this->makers = Cache::get('makers', function () {
            return Maker::query()->orderBy('name')->get();
        });*/

        /*// Remove from cache
        Cache::forget('makers');
        // Return value and remove from cache
        $makers = Cache::pull('makers');
        // Removes from cache
        Cache::put('makers', '', -1);
        // Clear the cache
        Cache::flush();*/

        /*// Shortcut function cache()
        // Gets value from cache
        $makers = cache('makers');
        // Sets value to the cache with ttl
        cache(['makers' => $makers], now()->addDays(30));
        // Deletes from cache
        cache(['makers' => $makers], -1);
        // Same remember forever as we did use facade
        $this->makers = cache()->rememberForever('makers', function() {
            return Maker::query()->orderBy('name')->get();
        });*/

        // Putting into the forever cache and automate renewal
        $this->makers = Cache::rememberForever('makers', function () {
            return Maker::query()->orderBy('name')->get();
        });

        // $this->makers = Maker::query()->orderBy('name')->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-maker');
    }
}
