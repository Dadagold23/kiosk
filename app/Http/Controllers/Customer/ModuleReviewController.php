<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ModuleReview;
use App\Services\ModuleReviewService;
use Illuminate\Http\Request;

class ModuleReviewController extends Controller
{
    public function store(Request $request, string $type, string $record, ModuleReviewService $moduleReviewService)
    {
        $reviewable = $moduleReviewService->resolveReviewable($type, $record);

        abort_unless($moduleReviewService->isOwnedBy($reviewable, $request->user()), 403);

        if (! $moduleReviewService->isEligible($reviewable)) {
            return back()->with('error', 'Reviews become available after this module has been completed and confirmed.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'review' => ['required', 'string', 'min:20', 'max:3000'],
            'would_recommend' => ['nullable', 'boolean'],
            'show_identity' => ['nullable', 'boolean'],
        ]);

        $reviewable->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'rating' => $validated['rating'],
                'title' => $validated['title'] ?? null,
                'review' => $validated['review'],
                'would_recommend' => $request->boolean('would_recommend'),
                'show_identity' => $request->boolean('show_identity'),
                'public_name' => $request->user()->name,
                'status' => ModuleReview::STATUS_PENDING,
                'is_featured' => false,
                'moderation_note' => null,
                'moderated_by' => null,
                'moderated_at' => null,
            ]
        );

        return back()->with('success', 'Your review has been submitted and is awaiting approval before it appears publicly.');
    }
}
