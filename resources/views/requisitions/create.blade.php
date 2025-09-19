@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header pb-0 d-flex align-items-center justify-content-between">
          <h6 class="mb-0">Add New Requisition</h6>
          <a href="{{ route('packages.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Packages
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

          <form action="{{ route('requisitions.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf

            {{-- Package snapshot (readonly) --}}
            <input type="hidden" name="package_id" value="{{ $package->id }}">
            <input type="hidden" name="package_no" value="{{ $package->package_no }}">

            <div class="row">
              <div class="col-md-6">
                <label class="form-control-label">Package ID</label>
                <input type="text" class="form-control" value="{{ $package->package_id }}" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-control-label">Package No</label>
                <input type="text" class="form-control" value="{{ $package->package_no }}" readonly>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-control-label">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $package->description) }}</textarea>
              </div>
              <div class="col-md-6">
                <label class="form-control-label">Requisition Status</label>
                <select name="requisition_status_id" class="form-control">
                  <option value="">-- Select Status --</option>
                  @foreach($statuses as $s)
                    <option value="{{ $s->id }}" @selected(old('requisition_status_id') == $s->id)>{{ $s->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-control-label">Procurement Method</label>
                <select name="procurement_method_id" class="form-control">
                  <option value="">-- Select Method --</option>
                  @foreach($methods as $m)
                    <option value="{{ $m->id }}" @selected(old('procurement_method_id') == $m->id)>{{ $m->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-control-label">Estimated Cost (BDT)</label>
                <input type="number" step="0.01" name="estimated_cost_bdt" class="form-control" value="{{ old('estimated_cost_bdt') }}">
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-control-label">Unit</label>
                <select name="unit_id" class="form-control">
                  <option value="">-- Select Unit --</option>
                  @foreach($units as $u)
                    <option value="{{ $u->id }}" @selected(old('unit_id') == $u->id)>{{ $u->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-control-label">Quantity / Nos.</label>
                <input type="number" step="0.001" name="quantity" class="form-control" value="{{ old('quantity') }}">
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-control-label">Name of Vendor</label>
                <input type="text" name="vendor_name" class="form-control" value="{{ old('vendor_name') }}">
              </div>
              <div class="col-md-6">
                <label class="form-control-label">Department</label>
                <select name="department_id" class="form-control">
                  <option value="">-- Select Department --</option>
                  @foreach($departments as $d)
                    <option value="{{ $d->id }}" @selected(old('department_id') == $d->id)>{{ $d->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-control-label">Type of Procurement</label>
                <select name="procurement_type_id" class="form-control">
                  <option value="">-- Select Type --</option>
                  @foreach($types as $t)
                    <option value="{{ $t->id }}" @selected(old('procurement_type_id') == $t->id)>{{ $t->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-control-label">Assigned Officer Name</label>
                <select name="officer_name" class="form-control">
                  <option value="">-- Select Officer --</option>
                  @foreach($officers as $off)
                    <option value="{{ $off->name }}" @selected(old('officer_name') == $off->name)>{{ $off->name }}</option>
                  @endforeach
                </select>
                @error('officer_name')
                  <div class="text-danger small">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-control-label">Official Estimated Cost (BDT)</label>
                <input type="number" step="0.01" name="official_estimated_cost_bdt" class="form-control" value="{{ old('official_estimated_cost_bdt') }}">
              </div>
              <div class="col-md-6">
                <label class="form-control-label">Requisition Receiving Date</label>
                <input type="date" name="requisition_receiving_date" class="form-control" value="{{ old('requisition_receiving_date') }}">
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-control-label">Delivery Date</label>
                <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date') }}">
              </div>
              <div class="col-md-6">
                <label class="form-control-label">Approving Authority</label>
                <select name="approving_authority_id" class="form-control">
                  <option value="">-- Select --</option>
                  @foreach($authorities as $a)
                    <option value="{{ $a->id }}" @selected(old('approving_authority_id') == $a->id)>{{ $a->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-control-label">Signing Date</label>
                <input type="date" name="signing_date" class="form-control" value="{{ old('signing_date') }}">
              </div>
              <div class="col-md-6">
                <label class="form-control-label">LC Status</label>
                <select name="lc_status_id" class="form-control">
                  <option value="">-- Select --</option>
                  @foreach($lcStatuses as $l)
                    <option value="{{ $l->id }}" @selected(old('lc_status_id') == $l->id)>{{ $l->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-control-label">Reference Link</label>
                <input type="url" name="reference_link" class="form-control" value="{{ old('reference_link') }}">
              </div>
              <div class="col-md-6">
                <label class="form-control-label">Tech Spec (Excel)</label>
                <input type="file" name="tech_spec" class="form-control" accept=".xlsx,.xls,.csv">
                <small class="text-muted">Headers: <code>spec_name, qty, unit_price, total_price</code></small>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-control-label">Reference Annex (file)</label>
                <input type="file" name="reference_annex" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-control-label">Comments</label>
                <textarea name="comments" class="form-control" rows="3">{{ old('comments') }}</textarea>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
              <a href="{{ route('packages.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
              <button type="submit" class="btn bg-gradient-primary">Submit Requisition</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
