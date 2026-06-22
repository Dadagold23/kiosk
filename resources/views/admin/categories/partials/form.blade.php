<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold small text-uppercase text-secondary">Type</label>
        <select name="type" class="form-select radius-30">
            @foreach(['product','service','consultancy','booking'] as $type)
                <option value="{{ $type }}" @selected(old('type', $category->type ?? '') === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold small text-uppercase text-secondary">Name</label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" class="form-control radius-30">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold small text-uppercase text-secondary">Icon</label>
        <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}" class="form-control radius-30" placeholder="Optional icon text or class">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold small text-uppercase text-secondary">Status</label>
        <select name="status" class="form-select radius-30">
            <option value="1" @selected((string) old('status', $category->status ?? '1') === '1')>Active</option>
            <option value="0" @selected((string) old('status', $category->status ?? '1') === '0')>Inactive</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label fw-bold small text-uppercase text-secondary">Description</label>
        <textarea name="description" rows="5" class="form-control radius-30">{{ old('description', $category->description ?? '') }}</textarea>
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-primary radius-30 px-4">{{ $category ? 'Update Category' : 'Create Category' }}</button>
    </div>
</div>

