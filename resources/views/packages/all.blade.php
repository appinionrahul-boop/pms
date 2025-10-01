@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <h6 class="mb-0">All Packages</h6>
      <div class="d-flex gap-2">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
          Back
        </a>
        <a href="{{ route('packages.download.excel') }}" class="btn btn-success btn-sm">
          Download Excel
        </a>
      </div>
    </div>

    <div class="card-body pt-3">
      <div class="table-responsive">
        <table id="packagesTable" class="table table-striped align-middle">
          <thead>
            <tr>
              <th>Package ID</th>
              <th>Package No</th>
              <th>Description</th>
              <th>Procurement Method</th>
              <th class='text-end'  >Estimated Cost (BDT)</th>
            </tr>
          </thead>
          <tbody>
            @forelse($packages as $p)
              <tr>
                <td>{{ $p->package_id }}</td>
                <td>{{ $p->package_no }}</td>
                <td>{{ $p->description }}</td>
                <td>{{ $p->procurement_method_name ?? '-' }}</td>
                <td class='text-end'>{{ number_format($p->estimated_cost_bdt, 2) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">No packages found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- DataTable script --}}
<script>
  $(function () {
    $('#packagesTable').DataTable({
      searching: false,   // ❌ disable search
      paging: true,
      ordering: true,
      info: true,
      pageLength: 10,
      order: [[0, 'asc']], // default order by Package ID
      columnDefs: [
        { targets: '_all', className: 'align-middle' }
      ],
      language: {
        emptyTable: 'No packages found.',
        zeroRecords: 'No matching records.',
        paginate: {
          previous: '&laquo;',
          next: '&raquo;'
        }
      }
    });
  });
</script>
@endsection
