@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Edit Requisition</h6>
      <a href="{{ route('requisitions.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Cancel
      </a>
    </div>

    <div class="card-body">
      <form action="{{ route('requisitions.update',$requisition) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Package info (readonly) --}}
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Package ID</label>
            <input type="text" class="form-control" value="{{ $requisition->package->package_id ?? '—' }}" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label">Package No</label>
            <input type="text" class="form-control" value="{{ $requisition->package_no }}" readonly>
          </div>
        </div>

        {{-- Description --}}
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="2">{{ old('description',$requisition->description) }}</textarea>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Requisition Status</label>
            <select name="requisition_status_id" class="form-control">
              <option value="">-- Select --</option>
              @foreach($statuses as $s)
                <option value="{{ $s->id }}" @selected(old('requisition_status_id',$requisition->requisition_status_id)==$s->id)>{{ $s->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Procurement Method</label>
            <select name="procurement_method_id" class="form-control">
              <option value="">-- Select --</option>
              @foreach($methods as $m)
                <option value="{{ $m->id }}" @selected(old('procurement_method_id',$requisition->procurement_method_id)==$m->id)>{{ $m->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Estimated Cost (BDT)</label>
            <input type="number" step="0.01" name="estimated_cost_bdt" class="form-control"
                   value="{{ old('estimated_cost_bdt',$requisition->estimated_cost_bdt) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Official Estimated Cost (BDT)</label>
            <input type="number" step="0.01" name="official_estimated_cost_bdt" class="form-control"
                   value="{{ old('official_estimated_cost_bdt',$requisition->official_estimated_cost_bdt) }}">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Unit</label>
            <select name="unit_id" class="form-control">
              <option value="">-- Select --</option>
              @foreach($units as $u)
                <option value="{{ $u->id }}" @selected(old('unit_id',$requisition->unit_id)==$u->id)>{{ $u->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Quantity</label>
            <input type="number" step="0.001" name="quantity" class="form-control"
                   value="{{ old('quantity',$requisition->quantity) }}">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Vendor</label>
            <input type="text" name="vendor_name" class="form-control"
                   value="{{ old('vendor_name',$requisition->vendor_name) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Department</label>
            <select name="department_id" class="form-control">
              <option value="">-- Select --</option>
              @foreach($departments as $d)
                <option value="{{ $d->id }}" @selected(old('department_id',$requisition->department_id)==$d->id)>{{ $d->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Type of Procurement</label>
            <select name="procurement_type_id" class="form-control">
              <option value="">-- Select --</option>
              @foreach($types as $t)
                <option value="{{ $t->id }}" @selected(old('procurement_type_id',$requisition->procurement_type_id)==$t->id)>{{ $t->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Approving Authority</label>
            <select name="approving_authority_id" class="form-control">
              <option value="">-- Select --</option>
              @foreach($authorities as $a)
                <option value="{{ $a->id }}" @selected(old('approving_authority_id',$requisition->approving_authority_id)==$a->id)>{{ $a->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">LC Status</label>
            <select name="lc_status_id" class="form-control">
              <option value="">-- Select --</option>
              @foreach($lcStatuses as $l)
                <option value="{{ $l->id }}" @selected(old('lc_status_id',$requisition->lc_status_id)==$l->id)>{{ $l->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Reference Link</label>
            <input type="url" name="reference_link" class="form-control"
                   value="{{ old('reference_link',$requisition->reference_link) }}">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Requisition Receiving Date</label>
            <input type="date" name="requisition_receiving_date" class="form-control"
                   value="{{ old('requisition_receiving_date',$requisition->requisition_receiving_date) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Delivery Date</label>
            <input type="date" name="delivery_date" class="form-control"
                   value="{{ old('delivery_date',$requisition->delivery_date) }}">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Signing Date</label>
            <input type="date" name="signing_date" class="form-control"
                   value="{{ old('signing_date',$requisition->signing_date) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Reference Annex</label><br>
            @if($requisition->reference_annex)
              <a href="{{ asset('storage/'.$requisition->reference_annex) }}" target="_blank">Current File</a><br>
            @endif
            <input type="file" name="reference_annex" class="form-control mt-2">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Tech Spec File</label><br>
            @if($requisition->tech_spec_file)
              <a href="{{ asset('storage/'.$requisition->tech_spec_file) }}" target="_blank">Current File</a><br>
            @endif
            <input type="file" name="tech_spec" class="form-control mt-2" accept=".xlsx,.xls,.csv">
          </div>
          <div class="col-md-6">
            <label class="form-label">Comments</label>
            <textarea name="comments" class="form-control" rows="3">{{ old('comments',$requisition->comments) }}</textarea>
          </div>
        </div>

        <div class="d-flex justify-content-end">
          <a href="{{ route('requisitions.show',$requisition) }}" class="btn btn-outline-secondary me-2">Cancel</a>
          <button type="submit" class="btn bg-gradient-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
