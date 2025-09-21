@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div class="mt-3">
        <a href="{{ route('techspecs.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
      </div>
      <div>
        <h6 class="mb-0">Technical Specs for Package</h6>
        <small>Package ID: <strong>{{ $package->package_id }}</strong> &nbsp; | &nbsp;
               Package No: <strong>{{ $package->package_no }}</strong></small>
      </div>
      <div>
        <a href="{{ route('techspecs.createForPackage', $package) }}" class="btn btn-sm bg-gradient-primary">
          <i class="fas fa-plus me-1"></i> Add New Spec
        </a>
      </div>
    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-start">Spec Name</th>
                <th class="text-end">Qty / Nos.</th>
                <th class="text-end">Unit Price (BDT)</th>
                <th class="text-end">Total Price (BDT)</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($specs as $s)
                <tr>
                  <td>{{ $s->spec_name }}</td>
                  {{-- force integer display --}}
                  <td class="text-end">{{ (int) $s->quantity }}</td>
                  <td class="text-end">{{ (int) ($s->unit_price_bdt ?? 0) }}</td>
                  <td class="text-end">{{ (int) ($s->total_price_bdt ?? 0) }}</td>
                  <td class="text-end">
                    <a href="{{ route('techspecs.edit', $s) }}" class="btn btn-link text-primary px-2 mb-0">
                      <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <form action="{{ route('techspecs.destroy', $s) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this specification?');">
                      @csrf @method('DELETE')
                      <button class="btn btn-link text-danger px-2 mb-0" type="submit">
                        <i class="fas fa-trash me-1"></i> Delete
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-secondary py-3">No specs for this package.</td>
                </tr>
                @endforelse
          </tbody>

        </table>
      </div>

      <div class="mt-3 px-3">
        {{ $specs->links() }}
      </div>



    </div>
  </div>
</div>
@endsection
