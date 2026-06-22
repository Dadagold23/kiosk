<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ModuleReviewService;

class ConsultancyController extends Controller
{
    public function index(ModuleReviewService $moduleReviewService)
    {
        $categories = Category::where('type', 'consultancy')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        $testimonials = $moduleReviewService->testimonialsFor('consultancy');

        return view('frontend.consultancy.index', compact('categories', 'testimonials'));
    }
}
