@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header pb-0 d-flex align-items-center justify-content-between">
          <h6 class="mb-0">Edit Package</h6>
          <a href="{{ route('packages.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to List
          </a>
        </div>

        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('packages.update', $package) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
              {{-- Package No --}}
              <div class="col-md-4">
                <div class="form-group">
                  <label class="form-control-label">Package No <span class="text-danger">*</span></label>
                  <input type="text" name="package_no"
                         value="{{ old('package_no', $package->package_no) }}"
                         class="form-control @error('package_no') is-invalid @enderror">
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
                      <option value="{{ $m->id }}" @selected(old('procurement_method_id', $package->procurement_method_id) == $m->id)>
                        {{ $m->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('procurement_method_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              {{-- Assigned Officer --}}
              <div class="col-md-4">
                <div class="form-group">
                  <label class="form-control-label">Assigned Officer</label>
                  <select name="assigned_officer_id"
                          class="form-control @error('assigned_officer_id') is-invalid @enderror">
                    <option value="">-- Select Officer --</option>
                    @foreach($officers as $officer)
                      <option value="{{ $officer->id }}"
                        @selected(old('assigned_officer_id', $package->assigned_officer_id) == $officer->id)>
                        {{ $officer->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('assigned_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>

            <div class="row">
              {{-- Fiscal Year --}}
              <div class="col-md-4">
                <div class="form-group">
                  <label class="form-control-label">Fiscal Year</label>
                  <select name="fiscal_year"
                          class="form-control @error('fiscal_year') is-invalid @enderror">
                    <option value="">-- Select Fiscal Year --</option>
                    @foreach($fiscalYears as $fy)
                      <option value="{{ $fy }}" @selected(old('fiscal_year', $package->fiscal_year) == $fy)>{{ $fy }}</option>
                    @endforeach
                  </select>
                  @error('fiscal_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              {{-- Estimated Cost --}}
              <div class="col-md-4">
                <div class="form-group">
                  <label class="form-control-label">Estimated Cost (BDT)</label>
                  <input type="number" step="0.01" name="estimated_cost_bdt"
                         value="{{ old('estimated_cost_bdt', $package->estimated_cost_bdt) }}"
                         class="form-control @error('estimated_cost_bdt') is-invalid @enderror">
                  @error('estimated_cost_bdt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              {{-- Description --}}
              <div class="col-md-8">
                <div class="form-group">
                  <label class="form-control-label">Description</label>
                  <textarea name="description" rows="3"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Short description">{{ old('description', $package->description) }}</textarea>
                  @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end">
              <a href="{{ route('packages.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
              <button type="submit" class="btn bg-gradient-primary">Update Package</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
