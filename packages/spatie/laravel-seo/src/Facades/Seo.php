<?php

namespace Spatie\LaravelSeo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Spatie\LaravelSeo\SeoManager title(?string $title)
 * @method static \Spatie\LaravelSeo\SeoManager description(?string $description)
 * @method static \Spatie\LaravelSeo\SeoManager keywords(string|array|null $keywords)
 * @method static \Spatie\LaravelSeo\SeoManager canonical(?string $url)
 * @method static \Spatie\LaravelSeo\SeoManager meta(string $name, string $content)
 * @method static string render()
 */
class Seo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'seo';
    }
}
