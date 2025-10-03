@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <div>
        <h6 class="mb-0">Technical Specifications</h6>
        <small class="text-muted">Search by Package ID/Package No/ERP Code/ Spec Name</small>
      </div>
      <div>
        <a href="{{ route('techspecs.create') }}" class="btn btn-sm bg-gradient-primary">
          <i class="fas fa-plus me-1"></i> Add New
        </a>
      </div>
    </div>

    <div class="card-body pt-3">
      <form method="GET" action="{{ route('techspecs.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
          <input type="text" name="q" class="form-control" value="{{ $q }}" placeholder="">
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-secondary w-100"><i class="fas fa-search me-1"></i> Search</button>
        </div>
        <div class="col-md-2">
          <a href="{{ route('techspecs.index') }}" class="btn btn-outline-dark w-100">Reset</a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table align-items-center mb-0" id="packagesTable" style="width:100%">
          <thead>
            <tr>
              <th>Package ID</th>
              <th>Package No</th>
              <th style="width:28%">Description</th>
              <th>ERP Code</th>
              <th>Spec Name</th>
              <th class="text-end">Quantity</th>
              <th class="text-end">Unit Price (BDT)</th>
              <th class="text-end">Total Price (BDT)</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($specs as $s)
              <tr>
                <td class="align-middle">{{ $s->package_id }}</td>
                <td class="align-middle">{{ $s->package_no }}</td>
                <td class="align-middle desc-cell">
                  <span class="desc-text">{{ $s->description }}</span>
                </td>
                <td class="align-middle">{{ $s->erp_code }}</td>
                <td class="align-middle">{{ $s->spec_name }}</td>
                <td class="text-end align-middle">{{ (int) $s->quantity }}</td>
                <td class="text-end align-middle">{{  $s->unit_price_bdt }}</td>
                <td class="text-end align-middle">{{  $s->total_price_bdt }}</td>
                <td class="text-end align-middle">
                  <!-- <a href="{{ route('techspecs.show', $s->package_id) }}" class="btn btn-link text-secondary px-2 mb-0">
                    <i class="fas fa-eye me-1"></i> View
                  </a> -->
                  <!-- <a href="{{ route('techspecs.edit', $s->package_id) }}" class="btn btn-link text-primary px-2 mb-0">
                    <i class="fas fa-edit me-1"></i> Edit
                  </a>
                  <form action="{{ route('techspecs.destroy', $s->package_id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Delete this specification?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger px-2 mb-0">
                      <i class="fas fa-trash me-1"></i> Delete
                    </button> -->
                    <a href="{{ route('techspecs.edit', $s->spec_id) }}" class="btn btn-link text-primary px-2 mb-0">
                      <i class="fas fa-edit me-1"></i> Edit
                    </a>

                    <form action="{{ route('techspecs.destroy', $s->spec_id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this specification?');">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger px-2 mb-0">
                        <i class="fas fa-trash me-1"></i> Delete
                      </button>
                  </form>

                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-3">No specifications found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3 px-3">
        {{-- no pagination since controller used ->get() --}}
      </div>
    </div>
  </div>
</div>

{{-- Wrapping rules for Description column --}}
<style>
  #packagesTable td { white-space: normal !important; }
  .desc-cell { max-width: 100%; }
  .desc-text {
    display: inline-block;
    max-width: 100%;
    word-break: keep-all;
    overflow-wrap: break-word;
  }
  @media (max-width: 768px) {
    .desc-cell { max-width: 100%; }
  }
</style>

{{-- jQuery + DataTables --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script>
  $(function () {
    $('#packagesTable').DataTable({
      order: [[1, 'asc']],                // by Package No
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      searching: false,                   // you’re using the top filter form
      columnDefs: [
        { targets: [5,6,7,8], className: 'text-end align-middle' }, // qty, unit, total, action
        { targets: [0,1,2,3,4], className: 'align-middle' },
        { targets: 8, orderable: false }  // action column not sortable
      ],
      language: {
        emptyTable: 'No specifications found.',
        zeroRecords: 'No matching specifications.'
      }
    });
  });
</script>

@endsection
