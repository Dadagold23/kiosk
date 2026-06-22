<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultancyRequest;
use App\Models\User;
use App\Notifications\RequestAssignedNotification;
use Illuminate\Http\Request;

class ConsultancyRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ConsultancyRequest::with('user', 'category', 'assignedConsultant');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('admin.consultancy.index', compact('requests'));
    }

    public function show(ConsultancyRequest $consultancyRequest)
    {
        $consultancyRequest->load('user', 'category', 'assignedConsultant', 'payments', 'reviews.user', 'reviews.moderator');

        $consultants = User::role(['Consultant', 'Consultant Manager', 'Admin'])->get();

        return view('admin.consultancy.show', compact('consultancyRequest', 'consultants'));
    }

    public function assign(Request $request, ConsultancyRequest $consultancyRequest)
    {
        $validated = $request->validate([
            'assigned_consultant_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'string', 'max:50'],
            'payment_status' => ['required', 'string', 'max:50'],
            'admin_note' => ['nullable', 'string', 'max:3000'],
            'report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:4096'],
        ]);

        if ($request->hasFile('report_file')) {
            $validated['report_file'] = $request->file('report_file')->store('consultancy-reports', 'public');
        }

        $consultancyRequest->update($validated);

        if ($consultancyRequest->user) {
            $consultancyRequest->user->notify(new RequestAssignedNotification(
                'consultancy',
                $consultancyRequest->subject,
                route('customer.consultancy.show', $consultancyRequest)
            ));
        }

        return back()->with('success', 'Consultancy request updated successfully.');
    }
}
