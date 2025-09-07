<?php

namespace App\View\Components;

use App\Models\Maker;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class SelectMaker extends Component
{
    public Collection $makers;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->makers = Maker::query()->orderBy('name')->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-maker');
    }
}
