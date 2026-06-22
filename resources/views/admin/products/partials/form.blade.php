<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label small text-secondary fw-bold text-uppercase">Category</label>
        <select name="category_id" class="form-select radius-30">
            <option value="">Select category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label small text-secondary fw-bold text-uppercase">Source Type</label>
        <select name="source_type" class="form-select radius-30">
            <option value="local" @selected(old('source_type', $product->source_type ?? 'local') === 'local')>Local</option>
            <option value="global" @selected(old('source_type', $product->source_type ?? '') === 'global')>Global</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label small text-secondary fw-bold text-uppercase">Marketplace</label>
        <input type="text" name="source_marketplace" value="{{ old('source_marketplace', $product->source_marketplace ?? '') }}" class="form-control radius-30" placeholder="Temu, Jumia, Alibaba...">
    </div>

    <div class="col-md-6">
        <label class="form-label small text-secondary fw-bold text-uppercase">Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control radius-30" required>
    </div>

    <div class="col-md-6">
        <label class="form-label small text-secondary fw-bold text-uppercase">SKU</label>
        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="form-control radius-30">
    </div>

    <div class="col-md-6">
        <label class="form-label small text-secondary fw-bold text-uppercase">External URL</label>
        <input type="url" name="external_url" value="{{ old('external_url', $product->external_url ?? '') }}" class="form-control radius-30">
    </div>

    <div class="col-md-4">
        <label class="form-label small text-secondary fw-bold text-uppercase">Price</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? 0) }}" class="form-control radius-30" required>
    </div>

    <div class="col-md-4">
        <label class="form-label small text-secondary fw-bold text-uppercase">Sale Price</label>
        <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" class="form-control radius-30">
    </div>

    <div class="col-md-4">
        <label class="form-label small text-secondary fw-bold text-uppercase">Quantity</label>
        <input type="number" name="quantity" value="{{ old('quantity', $product->quantity ?? 0) }}" class="form-control radius-30" required>
    </div>

    <div class="col-md-4">
        <label class="form-label small text-secondary fw-bold text-uppercase">Featured</label>
        <select name="featured" class="form-select radius-30">
            <option value="1" @selected((string) old('featured', $product->featured ?? '0') === '1')>Yes</option>
            <option value="0" @selected((string) old('featured', $product->featured ?? '0') === '0')>No</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label small text-secondary fw-bold text-uppercase">Status</label>
        <select name="status" class="form-select radius-30">
            <option value="1" @selected((string) old('status', $product->status ?? '1') === '1')>Active</option>
            <option value="0" @selected((string) old('status', $product->status ?? '1') === '0')>Inactive</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label small text-secondary fw-bold text-uppercase">Image</label>
        <input type="file" name="image" class="form-control radius-30">
    </div>

    <div class="col-12">
        <label class="form-label small text-secondary fw-bold text-uppercase">Description</label>
        <textarea name="description" rows="5" class="form-control radius-15">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary radius-30 px-4">Cancel</a>
        <button class="btn btn-primary radius-30 px-4">{{ isset($product) ? 'Update Product' : 'Create Product' }}</button>
    </div>
</div>
