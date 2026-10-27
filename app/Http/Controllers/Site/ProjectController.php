<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function show(string $slug): View
    {
        $project = Project::published()
            ->withMedia()
            ->where('slug', $slug)
            ->firstOrFail();

        $siblings = Project::published()->ordered()->get(['id', 'slug', 'title']);
        $position = $siblings->search(fn (Project $item) => $item->id === $project->id);
        $next = $siblings->get(($position + 1) % max($siblings->count(), 1)) ?? $project;

        return view('site.project', [
            'project' => $project,
            'next' => $next->is($project) ? null : $next,
            'title' => $project->title,
            'description' => $project->summary ?: (str($project->challenge ?? '')->stripTags()->limit(155)->toString() ?: null),
            'ogImage' => $project->coverUrl(),
            'metaType' => 'article',
        ]);
    }
}
