<?php

namespace App\View\Components;

use App\Models\CarFeature;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class CheckboxCarFeatures extends Component
{
    public array $features;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->features = Cache::rememberForever('features', function () {
            return CarFeature::featuresList();
        });
        //$this->features = CarFeature::featuresList();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.checkbox-car-features');
    }
}
