<?php

namespace App\Jobs;

use App\Models\Car;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TranslateJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     * Public argument is accessible in the scope
     */
    public function __construct(public Car $car)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger(sprintf('%s %s translated for editing.', $this->car->maker->name, $this->car->model->name));
    }
}
