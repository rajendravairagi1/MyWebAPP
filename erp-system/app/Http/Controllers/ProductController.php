<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('customer')->latest()->paginate(20);

        return view('products.index', ['products' => $products]);
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();

        return view('products.create', ['customers' => $customers, 'nextCode' => $this->nextCode()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'product_code' => ['required', 'string', 'max:50', 'unique:products,product_code'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'rate' => ['required', 'numeric', 'min:0'],
        ]);

        $product = Product::create($validated);

        return redirect()->route('products.index')->with('status', "{$product->name} add ho gaya.");
    }

    public function edit(Product $product)
    {
        $customers = Customer::orderBy('name')->get();

        return view('products.edit', ['product' => $product, 'customers' => $customers]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'product_code' => ['required', 'string', 'max:50', 'unique:products,product_code,'.$product->id],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'rate' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('status', 'Product update ho gaya.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('status', 'Product delete ho gaya.');
    }

    protected function nextCode(): string
    {
        $last = Product::max('id') ?? 0;

        return 'PRD-'.str_pad((string) ($last + 1), 4, '0', STR_PAD_LEFT);
    }
}
