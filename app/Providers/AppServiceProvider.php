<?php

namespace App\Providers;

use App\Listeners\LogCommandHistory;
use App\Listeners\LogJobHistory;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        Schema::defaultStringLength(191);

        Event::listen(JobProcessing::class, LogJobHistory::class.'@handleProcessing');
        Event::listen(JobProcessed::class, LogJobHistory::class.'@handleProcessed');
        Event::listen(JobFailed::class, LogJobHistory::class.'@handleFailed');

        Event::listen(CommandStarting::class, LogCommandHistory::class.'@handleStarting');
        Event::listen(CommandFinished::class, LogCommandHistory::class.'@handleFinished');
    }
}
