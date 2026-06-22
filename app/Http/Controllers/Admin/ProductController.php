<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\ActivityLogService;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $products = $query->latest()->paginate(24)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        $categories = Category::where('type', 'product')->where('status', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request, ActivityLogService $activityLogService)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'category_id'        => ['nullable', 'exists:categories,id'],
            'source_type'        => ['required', 'in:local,global'],
            'source_marketplace' => ['nullable', 'string', 'max:100'],
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:4000'],
            'sku'                => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'price'              => ['required', 'numeric', 'min:0'],
            'sale_price'         => ['nullable', 'numeric', 'min:0'],
            'quantity'           => ['required', 'integer', 'min:0'],
            'external_url'       => ['nullable', 'url'],
            'featured'           => ['required', 'boolean'],
            'status'             => ['required', 'boolean'],
            'image'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::lower(Str::random(5));

        $product = Product::create($validated);

        $activityLogService->log(
            auth()->id(),
            'product_created',
            Product::class,
            $product->id,
            'Created product: ' . $product->name
        );

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);

        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $categories = Category::where('type', 'product')->where('status', true)->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product, ActivityLogService $activityLogService)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'category_id'        => ['nullable', 'exists:categories,id'],
            'source_type'        => ['required', 'in:local,global'],
            'source_marketplace' => ['nullable', 'string', 'max:100'],
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:4000'],
            'sku'                => ['nullable', 'string', 'max:100', 'unique:products,sku,' . $product->id],
            'price'              => ['required', 'numeric', 'min:0'],
            'sale_price'         => ['nullable', 'numeric', 'min:0'],
            'quantity'           => ['required', 'integer', 'min:0'],
            'external_url'       => ['nullable', 'url'],
            'featured'           => ['required', 'boolean'],
            'status'             => ['required', 'boolean'],
            'image'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']) . '-' . $product->id;

        $product->update($validated);

        $activityLogService->log(
            auth()->id(),
            'product_updated',
            Product::class,
            $product->id,
            'Updated product: ' . $product->name
        );

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product, ActivityLogService $activityLogService)
    {
        $this->authorize('delete', $product);

        $name = $product->name;
        $id   = $product->id;
        $product->delete();

        $activityLogService->log(
            auth()->id(),
            'product_deleted',
            Product::class,
            $id,
            'Deleted product: ' . $name
        );

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
