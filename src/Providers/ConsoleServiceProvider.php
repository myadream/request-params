<?php

namespace LittleSuperman\RequestParams\Providers;

use Illuminate\Support\ServiceProvider;
use LittleSuperman\RequestParams\Foundation\Console\ModelMakeCommand;
use LittleSuperman\RequestParams\Routing\Console\ControllerMakeCommand;
use LittleSuperman\RequestParams\Foundation\Console\RequestMakeCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    /**
     * 注册命令
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RequestMakeCommand::class,
                ControllerMakeCommand::class,
                ModelMakeCommand::class
            ]);
        }
    }
}
