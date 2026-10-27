<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Inquiry::class);

        $inquiries = Inquiry::query()
            ->search($request->input('q'))
            ->status($request->input('status'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.inquiries.index', [
            'inquiries' => $inquiries,
            'filters' => $request->only(['q', 'status']),
            'counts' => [
                'all' => Inquiry::count(),
                'new' => Inquiry::where('status', Inquiry::STATUS_NEW)->count(),
                'read' => Inquiry::where('status', Inquiry::STATUS_READ)->count(),
                'replied' => Inquiry::where('status', Inquiry::STATUS_REPLIED)->count(),
            ],
        ]);
    }

    public function show(Inquiry $inquiry): View
    {
        $this->authorize('view', $inquiry);

        if ($inquiry->status === Inquiry::STATUS_NEW) {
            $inquiry->update(['status' => Inquiry::STATUS_READ]);
        }

        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function status(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $this->authorize('update', $inquiry);

        $validated = $request->validate([
            'status' => ['required', Rule::in([Inquiry::STATUS_NEW, Inquiry::STATUS_READ, Inquiry::STATUS_REPLIED])],
        ]);

        $inquiry->update($validated);

        return back()->with('status', 'Inquiry marked as '.$validated['status'].'.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $this->authorize('delete', $inquiry);

        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('status', 'Inquiry deleted.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Inquiry::class);

        $inquiries = Inquiry::query()
            ->search($request->input('q'))
            ->status($request->input('status'))
            ->latest()
            ->get();

        $filename = 'inquiries-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($inquiries) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Name', 'Email', 'Business type', 'Service', 'Budget', 'Message', 'Status', 'Received', 'IP']);

            $safe = fn ($value) => is_string($value) && preg_match('/^[=+\-@]/', $value)
                ? "'".$value
                : $value;

            foreach ($inquiries as $inquiry) {
                fputcsv($handle, [
                    $safe($inquiry->name),
                    $safe($inquiry->email),
                    $safe($inquiry->business_type),
                    $safe($inquiry->service),
                    $safe($inquiry->budget),
                    $safe($inquiry->message),
                    $inquiry->status,
                    $inquiry->created_at?->toDateTimeString(),
                    $safe($inquiry->ip_address),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
