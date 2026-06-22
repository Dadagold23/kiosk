<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $items = WishlistItem::query()
            ->with('product.category')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('customer.wishlist.index', compact('items'));
    }

    public function store(Product $product): RedirectResponse
    {
        WishlistItem::query()->firstOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Product saved to your wishlist.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        WishlistItem::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Product removed from your wishlist.');
    }
}
