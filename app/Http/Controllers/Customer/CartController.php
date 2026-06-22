<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cart->load('items.product');

        return view('customer.cart.index', compact('cart'));
    }

    public function store(Product $product)
    {
        if (!$product->status) {
            return back()->with('error', 'This product is not available.');
        }

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        $price = $product->current_price;

        if ($this->requiresInventoryStockCheck($product) && $product->quantity < (($existingItem?->qty ?? 0) + 1)) {
            return back()->with('error', 'Only ' . $product->quantity . ' unit(s) of this product are currently available.');
        }

        if ($existingItem) {
            $existingItem->qty += 1;
            $existingItem->subtotal = $existingItem->qty * $existingItem->unit_price;
            $existingItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'item_name' => $product->name,
                'source_type' => $product->source_type,
                'source_marketplace' => $product->source_marketplace,
                'qty' => 1,
                'unit_price' => $price,
                'subtotal' => $price,
                'meta' => [
                    'slug' => $product->slug,
                    'image' => $product->image,
                ],
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        $this->authorizeCartItem($item);

        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        if ($item->product && $this->requiresInventoryStockCheck($item->product) && $item->product->quantity < (int) $validated['qty']) {
            return back()->with('error', 'Only ' . $item->product->quantity . ' unit(s) of ' . $item->product->name . ' are currently available.');
        }

        $item->qty = (int) $validated['qty'];
        $item->subtotal = $item->qty * $item->unit_price;
        $item->save();

        return back()->with('success', 'Cart updated successfully.');
    }

    public function destroy(CartItem $item)
    {
        $this->authorizeCartItem($item);
        $item->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return back()->with('success', 'Cart cleared successfully.');
    }

    protected function authorizeCartItem(CartItem $item): void
    {
        abort_unless($item->cart && $item->cart->user_id === auth()->id(), 403);
    }

    protected function requiresInventoryStockCheck(Product $product): bool
    {
        return $product->source_type !== 'global';
    }
}
