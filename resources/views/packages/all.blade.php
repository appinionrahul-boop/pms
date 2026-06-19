@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <h6 class="mb-0">All Packages</h6>
      <div class="d-flex gap-2">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">Back</a>
        <a href="{{ route('packages.download.excel', request()->only('start','end')) }}" class="btn btn-success btn-sm">Download Excel</a>
      </div>
    </div>

    <div class="card-body pt-3">

      {{-- Date range filter (Created Date) --}}
      <form method="GET" action="{{ route('packages.all') }}" class="row g-2 align-items-end mb-3">
        <div class="col-md-3" style="margin-bottom: 15px;">
          <label class="form-label mb-1">Created From</label>
          <input type="date" name="start" class="form-control"
                 value="{{ request('start') }}">
        </div>
        <div class="col-md-3" style="margin-bottom: 15px;">
          <label class="form-label mb-1">Created To</label>
          <input type="date" name="end" class="form-control"
                 value="{{ request('end') }}">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label mb-1 invisible">Apply</label>
          <button class="btn btn-primary w-100">Apply</button>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label mb-1 invisible">Reset</label>
          <a href="{{ route('packages.all') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
      </form>

      {{-- Optional: show current filter --}}
      @if(request('start') || request('end'))
        <p class="text-muted">
          Showing packages created
          @if(request('start') && request('end'))
            from <strong>{{ \Carbon\Carbon::parse(request('start'))->format('d M Y') }}</strong>
            to <strong>{{ \Carbon\Carbon::parse(request('end'))->format('d M Y') }}</strong>
          @elseif(request('start'))
            from <strong>{{ \Carbon\Carbon::parse(request('start'))->format('d M Y') }}</strong> onwards
          @else
            until <strong>{{ \Carbon\Carbon::parse(request('end'))->format('d M Y') }}</strong>
          @endif
        </p>
      @endif

      <div class="table-responsive">
        <table id="packagesTable" class="table table-striped align-middle">
          <thead>
            <tr>
              <th>Package ID</th>
              <th>Package No</th>
              <th>Description</th>
              <th>Procurement Method</th>
              <th class="text-end">Estimated Cost (BDT)</th>
            </tr>
          </thead>
          <tbody>
            @forelse($packages as $p)
              <tr>
                <td>{{ $p->package_id }}</td>
                <td>{{ $p->package_no }}</td>
                <td>{{ $p->description }}</td>
                <td>{{ $p->procurement_method_name ?? '-' }}</td>
                <td class="text-end">{{ number_format((float)($p->estimated_cost_bdt ?? 0), 2) }}</td>
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

{{-- DataTable script (kept) --}}
<script>
  $(function () {
    $('#packagesTable').DataTable({
      searching: false,   // we filter via the form
      paging: true,
      ordering: true,
      info: true,
      pageLength: 10,
      order: [[0, 'asc']],
      columnDefs: [
        { targets: '_all', className: 'align-middle' }
      ],
      language: {
        emptyTable: 'No packages found.',
        zeroRecords: 'No matching records.',
        paginate: { previous: '&laquo;', next: '&raquo;' }
      }
    });
  });
</script>
@endsection
