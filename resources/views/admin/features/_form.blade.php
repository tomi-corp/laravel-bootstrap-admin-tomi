@csrf

@if ($feature->exists)
    @method('PUT')
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Kode <span class="text-danger">*</span></label>
            <input name="code" class="form-control" type="text" maxlength="50" value="{{ old('code', $feature->code) }}" required>
            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Nama <span class="text-danger">*</span></label>
            <input name="name" class="form-control" type="text" maxlength="100" value="{{ old('name', $feature->name) }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Status <span class="text-danger">*</span></label>
            <select name="status" class="form-control" required>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $feature->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $feature->description) }}</textarea>
            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>

<div class="text-right">
    <a href="{{ route('admin.features.index') }}" class="btn btn-secondary">Cancel</a>
    <button class="btn btn-primary" type="submit">Save</button>
</div>
