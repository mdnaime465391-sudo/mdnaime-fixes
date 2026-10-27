<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutPageRequest;
use App\Models\AboutPage;
use App\Support\HtmlSanitizer;
use App\Support\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function edit(): View
    {
        $this->authorize('update', AboutPage::class);

        return view('admin.about.edit', [
            'page' => $this->page(),
        ]);
    }

    public function update(AboutPageRequest $request, ImageService $images): RedirectResponse
    {
        $this->authorize('update', AboutPage::class);

        $page = $this->page();
        $data = $request->validated();

        unset($data['portrait']);
        $data['story'] = HtmlSanitizer::clean($data['story'] ?? null);
        $data['principles'] = $data['principles'] ?? [];
        $data['toolkit'] = $data['toolkit'] ?? [];

        $page->fill($data);

        if ($request->hasFile('portrait')) {
            $images->delete($page->portrait_path, false);
            $path = $images->storeAsWebp($request->file('portrait'), 'about', 1200, 640);

            if ($path) {
                $page->portrait_path = $path;
            }
        }

        $page->save();

        return back()->with('status', 'About page updated. Visible sections switch immediately.');
    }

    private function page(): AboutPage
    {
        return AboutPage::current() ?? new AboutPage([
            'eyebrow' => 'ABOUT',
            'headline' => 'I design the interface and build the system behind it.',
            'intro' => 'One person, the full pipeline: Figma UI/UX, frontend and the Laravel application behind it.',
            'story_heading' => 'A designer who ships, a developer who cares about the room.',
            'story' => '<p>I am Md Naime, a UI/UX designer and web developer in Dhaka. I work with med spas, clinics and service businesses that need a site to feel as considered as the experience they sell — and a system behind it that removes the admin work.</p>',
            'principles' => [
                ['title' => 'Clarity before decoration', 'body' => 'Every screen has a job. If a detail does not help a patient book or a team member work faster, it does not ship.'],
                ['title' => 'One pipeline, one owner', 'body' => 'Design and build stay in the same hands, so nothing is lost between Figma, frontend and Laravel.'],
                ['title' => 'Built to be handed over', 'body' => 'Clean structure, a real admin panel and a short walkthrough. Your team can run it without me.'],
            ],
            'toolkit' => config('portfolio.about_defaults.toolkit'),
            'availability_label' => 'Available for new projects',
            'show_story' => true,
            'show_principles' => true,
            'show_toolkit' => true,
            'show_testimonials' => true,
            'show_cta' => true,
        ]);
    }
}
