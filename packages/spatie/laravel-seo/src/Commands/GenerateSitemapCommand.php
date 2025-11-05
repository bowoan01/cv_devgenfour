<?php

namespace Spatie\LaravelSeo\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'seo:generate-sitemap {url? : The base URL to crawl}';

    protected $description = 'Generate a sitemap.xml file using Spatie\Sitemap';

    public function handle(): int
    {
        $baseUrl = $this->argument('url') ?: config('seo.canonical', config('app.url'));
        $targetPath = public_path('sitemap.xml');

        $this->info("Generating sitemap for {$baseUrl}...");

        SitemapGenerator::create($baseUrl)->writeToFile($targetPath);

        $this->info('Sitemap generated at '.$targetPath);

        return self::SUCCESS;
    }
}
