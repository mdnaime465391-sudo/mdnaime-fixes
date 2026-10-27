<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Support\ContentCache;
use App\Support\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectMediaController extends Controller
{
    public function store(Request $request, Project $project, ImageService $images): RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'type' => ['required', 'in:'.implode(',', ProjectMedia::TYPES)],
            'files' => ['required', 'array', 'max:12'],
            'files.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096'],
        ]);

        $order = (int) ($project->media()->max('sort_order') ?? 0);
        $saved = 0;

        foreach ($request->file('files') as $file) {
            $path = $images->storeAsWebp(
                $file,
                'projects/'.$project->id.'/'.$validated['type'],
                config('portfolio.uploads.gallery_width'),
                config('portfolio.uploads.gallery_thumb_width')
            );

            if (! $path) {
                continue;
            }

            $project->media()->create([
                'type' => $validated['type'],
                'path' => $path,
                'sort_order' => ++$order,
            ]);

            $saved++;
        }

        $redirectUrl = strtok((string) url()->previous(), '#').'#media-'.$validated['type'];

        return redirect()->to($redirectUrl)->with('status', $saved > 0
            ? $saved.' image'.($saved === 1 ? '' : 's').' uploaded to '.$validated['type'].'.'
            : 'No images could be processed. Use JPEG, PNG, WebP or AVIF under 4 MB.');
    }

    public function reorder(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        foreach ($validated['ids'] as $position => $id) {
            $project->media()->whereKey($id)->update(['sort_order' => $position + 1]);
        }

        ContentCache::project($project);

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, ProjectMedia $media): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $media->project);

        $validated = $request->validate([
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $media->update($validated);

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'caption' => $media->caption])
            : back()->with('status', 'Caption updated.');
    }

    public function destroy(ProjectMedia $media, ImageService $images): RedirectResponse
    {
        $this->authorize('update', $media->project);

        $images->delete($media->path);
        $media->delete();

        return back()->with('status', 'Image removed.');
    }
}
