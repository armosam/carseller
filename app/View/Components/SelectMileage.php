<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectMileage extends Component
{
    public array $mileages;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->mileages = [
            '10000' => '10.000 or less',
            '20000' => '20.000 or less',
            '30000' => '30.000 or less',
            '40000' => '40.000 or less',
            '50000' => '50.000 or less',
            '60000' => '60.000 or less',
            '70000' => '70.000 or less',
            '80000' => '80.000 or less',
            '90000' => '90.000 or less',
            '100000' => '100.000 or less',
            '150000' => '150.000 or less',
            '200000' => '200.000 or less',
            '250000' => '250.000 or less',
            '300000' => '300.000 or less',
            '350000' => '350.000 or less',
            '1000000' => 'More ...',
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-mileage');
    }
}
