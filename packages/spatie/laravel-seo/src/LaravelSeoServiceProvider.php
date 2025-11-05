<?php

namespace Spatie\LaravelSeo;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelSeo\Commands\GenerateSitemapCommand;

class LaravelSeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/seo.php', 'seo');

        $this->app->singleton(SeoManager::class, function ($app) {
            $config = $app['config']->get('seo', []);

            return new SeoManager($config);
        });

        $this->app->alias(SeoManager::class, 'seo');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/seo.php' => config_path('seo.php'),
        ], 'config');

        Blade::directive('seo', function () {
            return "<?php echo app('seo')->render(); ?>";
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateSitemapCommand::class,
            ]);
        }
    }
}
