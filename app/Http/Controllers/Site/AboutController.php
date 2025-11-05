<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\TeamMember;
use Spatie\LaravelSeo\Facades\Seo;

class AboutController extends Controller
{
    public function index()
    {
        Seo::title('About Devgenfour')
            ->description('Meet the Devgenfour team and discover our mission to build meaningful digital products.');

        $team = TeamMember::visible()->get();
        $timeline = json_decode(Setting::getValue('about_timeline', '[]'), true) ?? [];

        return view('site.about', compact('team', 'timeline'));
    }
}
