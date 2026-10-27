<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\ContentCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class WorkController extends Controller
{
    public function index(): View
    {
        $data = Cache::remember(ContentCache::WORK, ContentCache::SECONDS, function () {
            $projects = Project::published()->withMedia()->ordered()->get();

            return [
                'projects' => $projects,
                'categories' => $projects
                    ->flatMap(fn (Project $project) => $project->categories ?? [])
                    ->countBy()
                    ->sortDesc(),
                'liveCount' => $projects->filter(fn (Project $project) => $project->hasLivePreview())->count(),
            ];
        });

        return view('site.work', $data + [
            'title' => 'Projects',
            'description' => 'Selected med spa websites, WordPress builds and custom Laravel applications by Md Naime — designed in Figma and shipped end to end.',
        ]);
    }
}
