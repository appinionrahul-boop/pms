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
          <label for="date_from" class="form-label">Start Date</label>
          <input type="date" id="date_from" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
        </div>
        <div class="col-md-2">
          <label for="date_to" class="form-label">End Date</label>
          <input type="date" id="date_to" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
        </div>

        <div class="col-md-2">
          <button class="btn btn-outline-secondary w-100" type="submit" style="margin-top:28px">
            <i class="fas fa-filter me-1"></i> Search
          </button>
        </div>
        <div class="col-md-2">
          <a href="{{ route('requisitions.index') }}" class="btn btn-outline-dark w-100" style="margin-top:28px">Reset</a>
        </div>
      </form>

      {{-- Table --}}
      <div class="table-responsive">
        <table id="packagesTable" class="table table-flush align-items-center mb-0" style="width:100%">
          <thead class="thead-light">
            <tr>
              <th class="text-center">Package ID</th>
              <th class="text-center">Package No</th>
              <th class="text-center">Description</th>
              <th class="text-center">Procurement Method</th>
              <th class="text-center">Requisition Status</th>
              <th class="text-center">Name of Vendor</th>
              <th class="text-center">Department</th>
              <th class="text-center">Type of Procurement</th>
              <th class="text-center">Assigned Officer</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($requisitions as $r)
              <tr class="text-center">
                <td class="text-center">{{ $r->package->package_id ?? '—' }}</td>
                <td class="text-center">{{ $r->package_no ?? '—' }}</td>
                <td style="max-width:320px" class="text-center">{{ $r->description ?? '—' }}</td>
                <td class="text-center">{{ $r->method->name ?? '—' }}</td>
                <td class="text-center">{{ $r->status->name ?? '—' }}</td>
                <td class="text-center">{{ $r->vendor_name ?? '—' }}</td>
                <td class="text-center">{{ $r->department->name ?? '—' }}</td>
                <td class="text-center">{{ $r->procurementType->name ?? '—' }}</td>
                <td class="text-center">{{ $r->officer_name ?? '—' }}</td>
                <td class="text-center">
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

      {{-- No Laravel paginator here (DataTables handles paging) --}}

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
      order: [[1, 'asc']],           // sort by Package No
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      searching: false,              // search bar hidden (you have server-side filters)
      columnDefs: [
        { targets: 9, orderable: false, className: 'text-center align-middle' }, // Actions column
        { targets: [0,1,2,3,4,5,6,7,8], className: 'text-center align-middle' }
      ],
      language: {
        emptyTable: 'No requisitions found.',
        zeroRecords: 'No matching requisitions.'
      }
    });
  });
</script>
@endsection
