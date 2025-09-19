@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Edit Technical Specification</h6>
      <a href="{{ route('techspecs.show', $spec->package_id) }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
      @endif

      <form action="{{ route('techspecs.update', $spec) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
          <label class="form-label">Package</label>
          <select name="package_id" class="form-control" required>
            @foreach($packages as $p)
              <option value="{{ $p->id }}" @selected(old('package_id',$spec->package_id)==$p->id)>
                {{ $p->package_no }} — {{ $p->package_id }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Spec Name <span class="text-danger">*</span></label>
          <input type="text" name="spec_name" class="form-control" value="{{ old('spec_name',$spec->spec_name) }}" required>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Qty / Nos.</label>
            <input type="number" step="0.001" name="quantity" class="form-control" value="{{ old('quantity',$spec->quantity) }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Unit Price (BDT)</label>
            <input type="number" step="0.01" name="unit_price_bdt" class="form-control" value="{{ old('unit_price_bdt',$spec->unit_price_bdt) }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Total Price (BDT)</label>
            <input type="number" step="0.01" name="total_price_bdt" class="form-control" value="{{ old('total_price_bdt',$spec->total_price_bdt) }}">
            <small class="text-muted">If empty, we’ll auto-calc Qty × Unit Price.</small>
          </div>
        </div>

        <div class="d-flex justify-content-end">
          <button class="btn bg-gradient-primary" type="submit">Update Spec</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
