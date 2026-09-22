<?php

declare(strict_types=1);

namespace MorgancDev\McHarnessAi;

use Illuminate\Support\ServiceProvider;
use MorgancDev\McHarnessAi\Console\InstallAgentsCommand;

final class McHarnessServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallAgentsCommand::class,
            ]);
        }
    }
}
