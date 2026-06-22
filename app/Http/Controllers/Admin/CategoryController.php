<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\ActivityLogService;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $query = Category::query();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $categories = $query->latest()->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', Category::class);

        return view('admin.categories.create');
    }

    public function store(Request $request, ActivityLogService $activityLogService)
    {
        $this->authorize('create', Category::class);

        $validated = $request->validate([
            'type' => ['required', 'in:product,service,consultancy,booking'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
        ]);

        $category = Category::create([
            ...$validated,
            'slug' => Str::slug($validated['name']) . '-' . Str::lower(Str::random(4)),
        ]);

        $activityLogService->log(
            auth()->id(),
            'category_created',
            Category::class,
            $category->id,
            'Created category: ' . $category->name
        );

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }


    public function edit(Category $category)
    {
        $this->authorize('update', $category);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category, ActivityLogService $activityLogService)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'type' => ['required', 'in:product,service,consultancy,booking'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
        ]);

        $category->update([
            ...$validated,
            'slug' => Str::slug($validated['name']) . '-' . $category->id,
        ]);

        $activityLogService->log(
            auth()->id(),
            'category_updated',
            Category::class,
            $category->id,
            'Updated category: ' . $category->name
        );

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }


    public function destroy(Category $category, ActivityLogService $activityLogService)
    {
        $this->authorize('delete', $category);
        
        $name = $category->name;
        $id = $category->id;
        $category->delete();

        $activityLogService->log(
            auth()->id(),
            'category_deleted',
            Category::class,
            $id,
            'Deleted category: ' . $name
        );

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }

}
