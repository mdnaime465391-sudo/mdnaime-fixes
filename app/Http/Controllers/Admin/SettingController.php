<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;
use App\Support\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $this->authorize('update', Setting::class);

        return view('admin.settings.edit', [
            'settings' => Setting::current() ?? new Setting([
                'site_title' => 'Md Naime',
                'email' => 'hello@example.com',
                'social_links' => [],
            ]),
        ]);
    }

    public function update(SettingRequest $request, ImageService $images): RedirectResponse
    {
        $this->authorize('update', Setting::class);

        $settings = Setting::current() ?? new Setting(['id' => 1]);
        $data = $request->validated();

        unset($data['logo'], $data['favicon'], $data['og_image'], $data['hero_image']);
        $data['social_links'] = $data['social_links'] ?? [];

        $settings->fill($data);

        if ($request->hasFile('hero_image')) {
            $images->delete($settings->hero_image_path, false);
            $settings->hero_image_path = $images->storeAsWebp($request->file('hero_image'), 'brand', 1200, 640);
        }

        if ($request->hasFile('logo')) {
            $images->delete($settings->logo_path, false);
            $settings->logo_path = $images->storeAsWebp($request->file('logo'), 'brand', 800, null);
        }

        if ($request->hasFile('og_image')) {
            $images->delete($settings->og_image_path, false);
            $settings->og_image_path = $images->storeOriginal($request->file('og_image'), 'brand');
        }

        if ($request->hasFile('favicon')) {
            $images->delete($settings->favicon_path, false);
            $settings->favicon_path = $images->storeOriginal($request->file('favicon'), 'brand');
        }

        $settings->save();

        return back()->with('status', 'Settings saved. The public site is using them now.');
    }
}
