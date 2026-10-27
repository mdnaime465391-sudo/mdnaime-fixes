<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\Support\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Service::class);

        return view('admin.services.index', [
            'services' => Service::ordered()->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Service::class);

        return view('admin.services.create', [
            'service' => new Service(['is_published' => false, 'sort_order' => (Service::max('sort_order') ?? 0) + 1]),
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $this->authorize('create', Service::class);

        $data = $request->validated();
        $data['long_description'] = HtmlSanitizer::clean($data['long_description'] ?? null);

        $service = Service::create($data);

        return redirect()->route('admin.services.edit', $service)->with('status', 'Service created.');
    }

    public function edit(Service $service): View
    {
        $this->authorize('update', $service);

        return view('admin.services.edit', compact('service'));
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $this->authorize('update', $service);

        $data = $request->validated();
        $data['long_description'] = HtmlSanitizer::clean($data['long_description'] ?? null);
        $service->update($data);

        return redirect()->route('admin.services.edit', $service)->with('status', 'Service saved.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $this->authorize('delete', $service);

        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    public function togglePublish(Service $service, Request $request): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $service);

        $service->update(['is_published' => ! $service->is_published]);
        $message = $service->is_published ? 'Service published.' : 'Service moved to drafts.';

        return $request->expectsJson()
            ? response()->json(['published' => $service->is_published, 'message' => $message])
            : back()->with('status', $message);
    }
}
