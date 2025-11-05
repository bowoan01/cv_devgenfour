<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\LaravelSeo\Facades\Seo;

class ServiceController extends Controller
{
    public function index()
    {
        Seo::title('Services — Devgenfour')
            ->description('Discover Devgenfour services: custom software, UI/UX design, mobile development, and more.');

        $services = Service::published()->get();

        return view('site.services.index', compact('services'));
    }

    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)->where('is_published', true)->firstOrFail();

        Seo::title($service->title.' — Devgenfour')
            ->description(Str::limit($service->short_description ?: strip_tags($service->description), 155));

        return view('site.services.show', compact('service'));
    }
}
