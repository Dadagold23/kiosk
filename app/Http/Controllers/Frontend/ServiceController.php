<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ModuleReviewService;

class ServiceController extends Controller
{
    public function index(ModuleReviewService $moduleReviewService)
    {
        $categories = Category::where('type', 'service')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        $testimonials = $moduleReviewService->testimonialsFor('service');

        return view('frontend.services.index', compact('categories', 'testimonials'));
    }
}
