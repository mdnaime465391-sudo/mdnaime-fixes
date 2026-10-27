<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\InquiryRequest;
use App\Models\Inquiry;
use App\Models\Service;
use App\Models\Setting;
use App\Notifications\NewInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('site.contact', [
            'services' => Service::published()->ordered()->get(['id', 'title', 'slug']),
            'title' => 'Contact',
            'description' => 'Tell me about the clinic or the app. You will get a clear scope, a realistic timeline and a fixed quote.',
        ]);
    }

    public function store(InquiryRequest $request): RedirectResponse|JsonResponse
    {
        if ($request->filled('website')) {
            return $this->success($request);
        }

        $inquiry = Inquiry::create([
            'name' => $request->string('name')->trim()->toString(),
            'email' => $request->string('email')->trim()->toString(),
            'business_type' => $request->input('business_type'),
            'service' => $request->input('service'),
            'budget' => $request->input('budget'),
            'message' => $request->string('message')->trim()->toString(),
            'status' => Inquiry::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250),
        ]);

        $this->notifyAdmin($inquiry);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Inquiry received. I will reply within one business day.',
                'summary' => $this->summary($inquiry),
            ], 201);
        }

        return $this->success($request, $inquiry);
    }

    private function success(Request $request, ?Inquiry $inquiry = null): RedirectResponse|JsonResponse
    {
        $summary = $inquiry ? $this->summary($inquiry) : 'Inquiry received.';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Inquiry received. I will reply within one business day.',
                'summary' => $summary,
            ], 201);
        }

        return redirect()
            ->route('contact')
            ->with('inquiry_sent', $summary);
    }

    private function summary(Inquiry $inquiry): string
    {
        return collect([$inquiry->service, $inquiry->business_type, $inquiry->budget])
            ->filter()
            ->implode(' · ') ?: 'Project inquiry';
    }

    private function notifyAdmin(Inquiry $inquiry): void
    {
        $email = Setting::current()?->email ?: config('mail.from.address');

        if (! $email) {
            return;
        }

        try {
            Notification::route('mail', $email)->notify(new NewInquiry($inquiry));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
