<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\LaravelSeo\Facades\Seo;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');

        $projectsQuery = Project::published()->with('tags');

        if ($category) {
            $projectsQuery->where('category', $category);
        }

        $projects = $projectsQuery->paginate(9);
        $categories = Project::query()->select('category')->whereNotNull('category')->distinct()->pluck('category');

        Seo::title('Portfolio — Devgenfour')
            ->description('Explore software, mobile, and web projects delivered by Devgenfour.');

        return view('site.portfolio.index', compact('projects', 'categories', 'category'));
    }

    public function show(string $slug)
    {
        $project = Project::where('slug', $slug)->with(['images', 'tags'])->where('is_published', true)->firstOrFail();

        Seo::title($project->title.' — Case Study')
            ->description(Str::limit($project->summary, 155));

        $related = Project::published()->where('id', '!=', $project->id)->take(3)->get();

        return view('site.portfolio.show', compact('project', 'related'));
    }
}
