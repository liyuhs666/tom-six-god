<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// AppServiceProvider 是框架的核心，在 Laravel 启动时，会最先加载该文件
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.   
     *
     * @return void
     */
    public function boot()
	{
		\App\Models\User::observe(\App\Observers\UserObserver::class);
		\App\Models\Reply::observe(\App\Observers\ReplyObserver::class);
		\App\Models\Topic::observe(\App\Observers\TopicObserver::class);
        \Carbon\Carbon::setLocale('zh');   //启用时间格式化插件
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (app()->isLocal()) {
            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }
    }
}
