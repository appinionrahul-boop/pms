@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <div>
        <h6 class="mb-0">Requisition Management</h6>
      </div>
    </div>

    <div class="card-body pt-3">

      {{-- Filters --}}
      <form method="GET" action="{{ route('requisitions.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
          <input type="text" name="k" class="form-control"
                 value="{{ $filters['k'] ?? '' }}"
                 placeholder="Search (Package No / Vendor / Description)">
        </div>

        <div class="col-md-2">
          <select name="status_id" class="form-control">
            <option value=""> Requisition Status</option>
            @foreach($statuses as $s)
              <option value="{{ $s->id }}" @selected(($filters['status_id'] ?? null) == $s->id)>{{ $s->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <select name="procurement_type_id" class="form-control">
            <option value="">Type</option>
            @foreach($types as $t)
              <option value="{{ $t->id }}" @selected(($filters['procurement_type_id'] ?? null) == $t->id)>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <select name="procurement_method_id" class="form-control">
            <option value="">Method</option>
            @foreach($methods as $m)
              <option value="{{ $m->id }}" @selected(($filters['procurement_method_id'] ?? null) == $m->id)>{{ $m->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <select name="lc_status_id" class="form-control">
            <option value="">LC Status</option>
            @foreach($lcStatuses as $l)
              <option value="{{ $l->id }}" @selected(($filters['lc_status_id'] ?? null) == $l->id)>{{ $l->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}" placeholder="From">
        </div>
        <div class="col-md-2">
          <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}" placeholder="To">
        </div>

        <div class="col-md-2">
          <button class="btn btn-outline-secondary w-100" type="submit">
            <i class="fas fa-filter me-1"></i> Search
          </button>
        </div>
        <div class="col-md-2">
          <a href="{{ route('requisitions.index') }}" class="btn btn-outline-dark w-100">Reset</a>
        </div>
      </form>

      {{-- Table --}}
      <div class="table-responsive">
        <table id="packagesTable" class="table table-flush align-items-center mb-0" style="width:100%">
          <thead class="thead-light">
            <tr >
              <th class="text-center">Package ID</th>
              <th class="text-center">Package No</th>
              <th class="text-center">Description</th>
              <th class="text-center">Procurement Method</th>
              <th class="text-center">Requisition Status</th>
              <th class="text-center">Name of Vendor</th>
              <th class="text-center">Department</th>
              <th class="text-center">Type of Procurement</th>
              <th class="text-center">Assigned Officer</th>
              <!-- <th>Created</th> -->
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
  @foreach($requisitions as $r)
    <tr class="text-center">
      {{-- Package ID --}}
      <td class="text-center">{{ $r->package->package_id ?? '—' }}</td>

      {{-- Package No --}}
      <td class="text-center">{{ $r->package_no ?? '—' }}</td>

      {{-- Description --}}
      <td style="max-width:320px" class="text-center">{{ $r->description ?? '—' }}</td>

      {{-- Procurement Method --}}
      <td class="text-center">{{ $r->method->name ?? '—' }}</td>

      {{-- Requisition Status --}}
      <td class="text-center">{{ $r->status->name ?? '—' }}</td>

      {{-- Vendor Name --}}
      <td class="text-center">{{ $r->vendor_name ?? '—' }}</td>

      {{-- Department --}}
      <td class="text-center">{{ $r->department->name ?? '—' }}</td>

      {{-- Type of Procurement --}}
      <td class="text-center">{{ $r->procurementType->name ?? '—' }}</td>

      {{-- Assigned Officer (officer_name column) --}}
      <td class="text-center">{{ $r->officer_name ?? '—' }}</td>

      {{-- Created Date --}}
      <!-- <td>{{ optional($r->created_at)->format('Y-m-d') }}</td> -->

      {{-- Actions --}}
      <td class="text-cebter">
        <a href="{{ route('requisitions.show', $r) }}" class="btn btn-link text-secondary px-2 mb-0">
          <i class="fas fa-eye me-1"></i> View
        </a>
        <a href="{{ route('requisitions.edit', $r) }}" class="btn btn-link text-primary px-2 mb-0">
          <i class="fas fa-edit me-1"></i> Edit
        </a>
        <form action="{{ route('requisitions.destroy', $r) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Delete this requisition?');">
          @csrf @method('DELETE')
          <button class="btn btn-link text-danger px-2 mb-0" type="submit">
            <i class="fas fa-trash me-1"></i> Delete
          </button>
        </form>
      </td>
    </tr>
  @endforeach
</tbody>

        </table>
      </div>

      {{-- Optional: if you rely on DataTables paging, remove Laravel paginator below --}}
      {{-- <div class="mt-3 px-3">
        {{ $requisitions->links() }}
      </div> --}}
    </div>
  </div>
</div>

{{-- jQuery + DataTables CDN --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script>
  $(function () {
    $('#packagesTable').DataTable({
      order: [[6, 'desc']],                // sort by Created (7th col index = 6)
      pageLength: 10,
       searching: false,  
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [
        { targets: 7, orderable: false, className: 'text-end align-middle' }, // Actions
        { targets: 5, className: 'text-end align-middle' },                   // Est. Cost align right
        { targets: [0,1,2,3,4,6], className: 'align-middle' }
      ],
      language: {
        // search: 'Search:',
        // searchPlaceholder: 'Package ID / Package No...',
        emptyTable: 'No requisitions found.',
        zeroRecords: 'No matching requisitions.'
      }
    });
  });
</script>
@endsection
