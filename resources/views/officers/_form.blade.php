@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="row">
  <div class="col-md-6">
    <label class="form-control-label">Officer Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $officer->name ?? '') }}" required maxlength="255">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-control-label">Status</label>
    <select name="is_active" class="form-control">
      <option value="1" @selected((int) old('is_active', $officer->is_active ?? 1) === 1)>Active</option>
      <option value="0" @selected((int) old('is_active', $officer->is_active ?? 1) === 0)>Inactive</option>
    </select>
    <small class="text-muted">
      Inactive officers stay on their existing packages and requisitions, but are not
      offered when creating a package or adding a requisition.
    </small>
  </div>
</div>

<div class="d-flex justify-content-end mt-4">
  <a href="{{ route('officers.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
  <button type="submit" class="btn bg-gradient-primary">{{ $submitLabel }}</button>
</div>
