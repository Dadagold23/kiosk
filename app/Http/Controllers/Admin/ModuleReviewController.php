<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleReview;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuleReviewController extends Controller
{
    public function moderate(Request $request, ModuleReview $moduleReview)
    {
        abort_unless($moduleReview->canBeModeratedBy($request->user()), 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                ModuleReview::STATUS_PENDING,
                ModuleReview::STATUS_APPROVED,
                ModuleReview::STATUS_REJECTED,
            ])],
            'is_featured' => ['nullable', 'boolean'],
            'moderation_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $status = $validated['status'];

        $moduleReview->update([
            'status' => $status,
            'is_featured' => $status === ModuleReview::STATUS_APPROVED
                ? $request->boolean('is_featured')
                : false,
            'moderation_note' => $validated['moderation_note'] ?? null,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        return back()->with('success', 'Customer review moderation was saved successfully.');
    }
}
