<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->when($request->string('q')->trim()->isNotEmpty(), fn ($query) => $query->where('name', 'like', '%'.$request->string('q')->trim().'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validated($request));

        return redirect()->route('products.index')->with('status', 'Product added.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request));

        return redirect()->route('products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('status', 'Product removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:product,service'],
            'unit' => ['nullable', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
