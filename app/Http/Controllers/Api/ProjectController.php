<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProjectResource::collection(
            Project::published()->withMedia()->ordered()->get()
        );
    }

    public function show(string $slug): ProjectResource
    {
        $project = Project::published()->withMedia()->where('slug', $slug)->firstOrFail();

        return new ProjectResource($project);
    }
}
