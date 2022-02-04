<?php

namespace Hmones\LaravelDigest;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class LaravelDigestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('digest:send daily')->dailyAt(config('laravel-digest.frequency.daily.time'));
            $schedule->command('digest:send weekly')->weeklyOn(config('laravel-digest.frequency.weekly.day'), config('laravel-digest.frequency.weekly.time'));
            $schedule->command('digest:send monthly')->monthlyOn(config('laravel-digest.frequency.monthly.day'), config('laravel-digest.frequency.monthly.time'));
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-digest.php', 'laravel-digest');

        $this->app->singleton('digest', fn () => new LaravelDigest());
    }

    public function provides(): array
    {
        return ['laravel-digest'];
    }

    protected function bootForConsole(): void
    {
        $this->publishes([
            __DIR__.'/../config/laravel-digest.php' => config_path('laravel-digest.php'),
        ], 'laravel-digest.config');
    }
}