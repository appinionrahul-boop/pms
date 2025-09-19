@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header pb-0 d-flex align-items-center justify-content-between">
          <h6 class="mb-0">Add New Package</h6>
          <a href="{{ route('packages.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to List
          </a>
        </div>

        <div class="card-body">
          {{-- Flash / errors --}}
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('packages.store') }}" method="POST" autocomplete="off">
            @csrf

            <div class="row">
              {{-- Auto 6-digit Package ID (readonly) --}}
              <div class="col-md-4">
                <div class="form-group">
                  <label class="form-control-label">Package ID (auto)</label>
                  <input type="text"
                         class="form-control @error('package_id') is-invalid @enderror"
                         name="package_id"
                         value="{{ old('package_id', $generatedId ?? '') }}"
                         readonly>
                  @error('package_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              {{-- Unique Package No. --}}
              <div class="col-md-4">
                <div class="form-group">
                  <label class="form-control-label">Package No. <span class="text-danger">*</span></label>
                  <input type="text"
                         class="form-control @error('package_no') is-invalid @enderror"
                         name="package_no"
                         placeholder="e.g., G-01A"
                         value="{{ old('package_no') }}">
                  @error('package_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              {{-- Procurement Method --}}
              <div class="col-md-4">
                <div class="form-group">
                  <label class="form-control-label">Procurement Method</label>
                  <select name="procurement_method_id"
                          class="form-control @error('procurement_method_id') is-invalid @enderror">
                    <option value="">-- Select Method --</option>
                    @foreach($methods as $m)
                      <option value="{{ $m->id }}"
                        @selected(old('procurement_method_id') == $m->id)>
                        {{ $m->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('procurement_method_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>

            <div class="row">
              {{-- Estimated Cost (BDT) --}}
              <div class="col-md-4">
                <div class="form-group">
                  <label class="form-control-label">Estimated Cost (BDT)</label>
                  <input type="number" step="0.01"
                         class="form-control @error('estimated_cost_bdt') is-invalid @enderror"
                         name="estimated_cost_bdt"
                         placeholder="0.00"
                         value="{{ old('estimated_cost_bdt') }}">
                  @error('estimated_cost_bdt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              {{-- Description --}}
              <div class="col-md-8">
                <div class="form-group">
                  <label class="form-control-label">Description</label>
                  <textarea class="form-control @error('description') is-invalid @enderror"
                            name="description"
                            rows="3"
                            placeholder="Short description of the package">{{ old('description') }}</textarea>
                  @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end">
              <a href="{{ route('packages.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
              <button type="submit" class="btn bg-gradient-primary">Save Package</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
