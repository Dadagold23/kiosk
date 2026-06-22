<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\WishlistItem;
use App\Services\ModuleReviewService;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, ModuleReviewService $moduleReviewService)
    {
        $query = Product::query()
            ->with('category')
            ->where('status', true);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        $categories = Category::where('type', 'product')
            ->where('status', true)
            ->withCount(['products' => function ($query) {
                $query->where('status', true);
            }])
            ->orderBy('name')
            ->get();

        $featuredProducts = Product::query()
            ->with('category')
            ->where('status', true)
            ->where('featured', true)
            ->latest()
            ->take(4)
            ->get();

        $catalogStats = [
            'all' => Product::where('status', true)->count(),
            'local' => Product::where('status', true)->where('source_type', 'local')->count(),
            'global' => Product::where('status', true)->where('source_type', 'global')->count(),
            'featured' => Product::where('status', true)->where('featured', true)->count(),
        ];

        $wishlistProductIds = auth()->check()
            ? WishlistItem::query()->where('user_id', auth()->id())->pluck('product_id')->all()
            : [];

        $testimonials = $moduleReviewService->testimonialsFor('order');
        $catalogFocusImages = $this->catalogFocusImages();
        $sourceGuideImages = $this->sourceGuideImages();

        return view('frontend.shop.index', compact('products', 'categories', 'featuredProducts', 'catalogStats', 'testimonials', 'wishlistProductIds', 'catalogFocusImages', 'sourceGuideImages'));
    }

    public function show($slug)
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $relatedProducts = Product::where('status', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->latest()
            ->take(4)
            ->get();

        $wishlistProductIds = auth()->check()
            ? WishlistItem::query()->where('user_id', auth()->id())->pluck('product_id')->all()
            : [];

        return view('frontend.shop.show', compact('product', 'relatedProducts', 'wishlistProductIds'));
    }

    protected function catalogFocusImages(): Collection
    {
        $picked = collect([
            ['folder' => 'category', 'file' => 'cate-48.jpg', 'label' => 'Fresh picks'],
            ['folder' => 'category', 'file' => 'cate-50.jpg', 'label' => 'Popular finds'],
            ['folder' => 'category', 'file' => 'cate-45.jpg', 'label' => 'Daily essentials'],
            ['folder' => 'collection', 'file' => 'cls-12.jpg', 'label' => 'New arrivals'],
        ]);

        return $picked
            ->filter(function (array $image) {
                return is_file(public_path("assets/images/{$image['folder']}/{$image['file']}"));
            })
            ->map(function (array $image) {
                $path = "assets/images/{$image['folder']}/{$image['file']}";

                return [
                    'label' => $image['label'],
                    'url' => asset($path),
                ];
            })
            ->values();
    }

    protected function sourceGuideImages(): Collection
    {
        $picked = collect([
            ['folder' => 'collection', 'file' => 'cls-5.jpg', 'label' => 'Local stock'],
            ['folder' => 'collection', 'file' => 'cls-18.jpg', 'label' => 'Sourced orders'],
            ['folder' => 'category', 'file' => 'cate-21.jpg', 'label' => 'Faster delivery'],
            ['folder' => 'category', 'file' => 'cate-33.jpg', 'label' => 'Price confirmation'],
        ]);

        return $picked
            ->filter(function (array $image) {
                return is_file(public_path("assets/images/{$image['folder']}/{$image['file']}"));
            })
            ->map(function (array $image) {
                $path = "assets/images/{$image['folder']}/{$image['file']}";

                return [
                    'label' => $image['label'],
                    'url' => asset($path),
                ];
            })
            ->values();
    }
}
