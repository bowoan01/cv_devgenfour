<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use Spatie\LaravelSeo\Facades\Seo;

class HomeController extends Controller
{
    public function index()
    {
        Seo::title('Devgenfour — We build digital products that empower your business.')
            ->description('Devgenfour is a software house delivering custom software development, UI/UX design, and product strategy.');

        $services = Service::published()->take(4)->get();
        $projects = Project::published()->with('images')->take(4)->get();
        $team = TeamMember::visible()->take(4)->get();

        return view('site.home', compact('services', 'projects', 'team'));
    }
}
