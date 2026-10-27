<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Project;
use App\Support\HtmlSanitizer;
use App\Support\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::query()
            ->withCount('media')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('slug', 'like', $term));
            })
            ->when($request->input('status') === 'published', fn ($q) => $q->where('is_published', true))
            ->when($request->input('status') === 'draft', fn ($q) => $q->where('is_published', false))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'filters' => $request->only(['q', 'status']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        return view('admin.projects.create', [
            'project' => new Project(['is_published' => false, 'sort_order' => (Project::max('sort_order') ?? 0) + 1]),
            'categories' => $this->categories(),
        ]);
    }

    public function store(ProjectRequest $request, ImageService $images): RedirectResponse
    {
        $this->authorize('create', Project::class);

        $data = $request->validated();
        unset($data['cover_image'], $data['thumbnail']);

        $project = new Project($this->cleanRichText($data));
        $project->is_published = $request->boolean('is_published');
        $project->published_at = $project->is_published ? now() : null;
        $project->save();

        $this->storeCover($request, $project, $images);
        $project->save();

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('status', 'Project created. Add gallery images below, then review it on the site.');
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        $project->load('media');

        return view('admin.projects.edit', [
            'project' => $project,
            'categories' => $this->categories(),
        ]);
    }

    public function update(ProjectRequest $request, Project $project, ImageService $images): RedirectResponse
    {
        $this->authorize('update', $project);

        $data = $request->validated();
        unset($data['cover_image'], $data['thumbnail']);

        $wasPublished = $project->is_published;
        $project->fill($this->cleanRichText($data));
        $project->is_published = $request->boolean('is_published');

        if ($project->is_published && ! $project->published_at) {
            $project->published_at = now();
        }

        $this->storeCover($request, $project, $images);
        $project->save();

        $message = $project->is_published && ! $wasPublished
            ? 'Project published — it is live on the site now.'
            : 'Project saved.';

        return redirect()->route('admin.projects.edit', $project)->with('status', $message);
    }

    public function destroy(Project $project, ImageService $images): RedirectResponse
    {
        $this->authorize('delete', $project);

        foreach ($project->media as $media) {
            $images->delete($media->path);
        }

        $images->delete($project->cover_image);
        $images->delete($project->thumbnail);

        $project->delete();

        return redirect()->route('admin.projects.index')->with('status', 'Project deleted.');
    }

    public function togglePublish(Project $project, Request $request): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $project);

        $project->is_published = ! $project->is_published;

        if ($project->is_published && ! $project->published_at) {
            $project->published_at = now();
        }

        $project->save();

        $message = $project->is_published ? 'Project published.' : 'Project moved to drafts.';

        return $request->expectsJson()
            ? response()->json(['published' => $project->is_published, 'message' => $message])
            : back()->with('status', $message);
    }

    private function storeCover(ProjectRequest $request, Project $project, ImageService $images): void
    {
        $directory = 'projects/'.$project->id;

        if ($request->hasFile('cover_image')) {
            $images->delete($project->cover_image);
            $images->delete($project->thumbnail);

            $path = $images->storeAsWebp(
                $request->file('cover_image'),
                $directory,
                config('portfolio.uploads.cover_width'),
                config('portfolio.uploads.thumb_width')
            );

            if ($path) {
                $project->cover_image = $path;
                $project->thumbnail = (string) preg_replace('/\.webp$/', '-thumb.webp', $path);
            }
        }

        if ($request->hasFile('thumbnail')) {
            $images->delete($project->thumbnail);

            $thumb = $images->storeAsWebp(
                $request->file('thumbnail'),
                $directory.'/thumbs',
                config('portfolio.uploads.thumb_width'),
                null
            );

            if ($thumb) {
                $project->thumbnail = $thumb;
            }
        }
    }

    /** @return array<int, string> */
    private function categories(): array
    {
        return Project::query()
            ->pluck('categories')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function cleanRichText(array $data): array
    {
        foreach (['background', 'challenge', 'design_approach', 'solution', 'result'] as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = HtmlSanitizer::clean($data[$field]);
            }
        }

        return $data;
    }
}
