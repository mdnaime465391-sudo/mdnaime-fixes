<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Testimonial;
use App\Support\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Testimonial::class);

        return view('admin.testimonials.index', [
            'testimonials' => Testimonial::ordered()->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Testimonial::class);

        return view('admin.testimonials.create', [
            'testimonial' => new Testimonial(['is_published' => false, 'sort_order' => (Testimonial::max('sort_order') ?? 0) + 1]),
        ]);
    }

    public function store(TestimonialRequest $request, ImageService $images): RedirectResponse
    {
        $this->authorize('create', Testimonial::class);

        $data = $request->validated();
        unset($data['avatar']);

        $testimonial = new Testimonial($data);
        $this->storeAvatar($request, $testimonial, $images);
        $testimonial->save();

        return redirect()->route('admin.testimonials.edit', $testimonial)->with('status', 'Testimonial created.');
    }

    public function edit(Testimonial $testimonial): View
    {
        $this->authorize('update', $testimonial);

        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial, ImageService $images): RedirectResponse
    {
        $this->authorize('update', $testimonial);

        $data = $request->validated();
        unset($data['avatar']);

        $testimonial->fill($data);
        $this->storeAvatar($request, $testimonial, $images);
        $testimonial->save();

        return redirect()->route('admin.testimonials.edit', $testimonial)->with('status', 'Testimonial saved.');
    }

    public function destroy(Testimonial $testimonial, ImageService $images): RedirectResponse
    {
        $this->authorize('delete', $testimonial);

        $images->delete($testimonial->avatar);
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial deleted.');
    }

    public function togglePublish(Testimonial $testimonial, Request $request): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $testimonial);

        $testimonial->update(['is_published' => ! $testimonial->is_published]);
        $message = $testimonial->is_published ? 'Testimonial published.' : 'Testimonial moved to drafts.';

        return $request->expectsJson()
            ? response()->json(['published' => $testimonial->is_published, 'message' => $message])
            : back()->with('status', $message);
    }

    private function storeAvatar(TestimonialRequest $request, Testimonial $testimonial, ImageService $images): void
    {
        if (! $request->hasFile('avatar')) {
            return;
        }

        $images->delete($testimonial->avatar, false);

        $path = $images->storeAsWebp(
            $request->file('avatar'),
            'testimonials',
            config('portfolio.uploads.avatar_width'),
            200
        );

        if ($path) {
            $testimonial->avatar = $path;
        }
    }
}
