<?php

namespace LittleSuperman\RequestParams\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * 配置文件注册
 *
 * @package LittleSuperman\RequestParams\Providers
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConfig();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigPath().'dynamicQuery.php', 'dynamicQuery');
    }

    /**
     * 注册配置
     *
     * @return ConfigServiceProvider
     */
    protected function registerConfig(): self
    {
        $this->publishes([
            $this->getConfigPath().'dynamicQuery.php' => config_path('dynamicQuery')
        ]);

        return $this;
    }

    /**
     * 获取配置地址
     *
     * @return string
     */
    protected function getConfigPath(): string
    {
        return __DIR__.'/../../resources/config/';
    }
}
