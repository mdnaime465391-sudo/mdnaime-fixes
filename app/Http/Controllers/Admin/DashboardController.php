<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Project::class);

        return view('admin.dashboard', [
            'stats' => [
                'projects' => Project::count(),
                'published' => Project::published()->count(),
                'services' => Service::count(),
                'testimonials' => Testimonial::count(),
                'inquiries' => Inquiry::count(),
                'unread' => Inquiry::where('status', Inquiry::STATUS_NEW)->count(),
                'replied' => Inquiry::where('status', Inquiry::STATUS_REPLIED)->count(),
            ],
            'recentInquiries' => Inquiry::latest()->take(5)->get(),
            'recentProjects' => Project::query()->withCount('media')->latest('updated_at')->take(5)->get(),
            'latestPublished' => Project::published()->ordered()->take(3)->get(),
        ]);
    }
}
