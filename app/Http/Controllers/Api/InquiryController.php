<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\InquiryRequest;
use App\Models\Inquiry;
use App\Models\Setting;
use App\Notifications\NewInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class InquiryController extends Controller
{
    public function store(InquiryRequest $request): JsonResponse
    {
        if ($request->filled('website')) {
            return response()->json(['ok' => true, 'message' => 'Inquiry received.'], 201);
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

        $email = Setting::current()?->email ?: config('mail.from.address');

        if ($email) {
            try {
                Notification::route('mail', $email)->notify(new NewInquiry($inquiry));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => 'Inquiry received. I will reply within one business day.',
            'data' => ['id' => $inquiry->id],
        ], 201);
    }
}
