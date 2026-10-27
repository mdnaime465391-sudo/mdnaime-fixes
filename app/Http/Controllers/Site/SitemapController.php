<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $projects = Project::published()->ordered()->get(['slug', 'updated_at']);

        return response()
            ->view('site.sitemap', compact('projects'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
