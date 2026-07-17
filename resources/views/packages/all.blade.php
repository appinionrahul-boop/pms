@extends('layouts.user_type.auth')

@section('content')

<style>
  /* keep option text clear of the dropdown caret */
  select.form-control, select.form-select{
    padding-right: 2.25rem !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>

<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <div>
        <h6 class="mb-0">All Packages</h6>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">Back</a>
        <a href="{{ route('packages.download.excel', request()->only('start','end','officer_id','fiscal_year')) }}" class="btn btn-success btn-sm">Download Excel</a>
      </div>
    </div>

    <div class="card-body pt-3">

      {{-- Filters --}}
      <form method="GET" action="{{ route('packages.all') }}" class="row g-2 mb-3">
          {{-- Created From --}}
          <div class="col-md-2">
            <label for="start" class="form-label">Created From</label>
            <input type="date" id="start" name="start" class="form-control" value="{{ request('start') }}">
          </div>

          {{-- Created To --}}
          <div class="col-md-2">
            <label for="end" class="form-label">Created To</label>
            <input type="date" id="end" name="end" class="form-control" value="{{ request('end') }}">
          </div>

          {{-- Assigned Officer --}}
          <div class="col-md-2">
            <label for="officer_id" class="form-label">Assigned Officer</label>
            <select id="officer_id" name="officer_id" class="form-control">
              <option value="">All</option>
              @foreach($officers as $officer)
                <option value="{{ $officer->id }}" @selected(request('officer_id') == $officer->id)>{{ $officer->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Fiscal Year --}}
          <div class="col-md-2">
            <label for="fiscal_year" class="form-label">Fiscal Year</label>
            <select id="fiscal_year" name="fiscal_year" class="form-control">
              <option value="">All</option>
              @foreach($fiscalYears as $fy)
                <option value="{{ $fy }}" @selected(request('fiscal_year') === $fy)>{{ $fy }}</option>
              @endforeach
            </select>
          </div>

          {{-- Search --}}
          <div class="col-md-2 d-grid">
            <label class="form-label invisible">Search</label>
            <button class="btn btn-outline-secondary w-100" type="submit">
              <i class="fas fa-filter me-1"></i> Search
            </button>
          </div>

          {{-- Reset --}}
          <div class="col-md-2 d-grid">
            <label class="form-label invisible">Reset</label>
            <a href="{{ route('packages.all') }}" class="btn btn-outline-dark w-100">Reset</a>
          </div>
        </form>

      {{-- Optional: show current filter --}}
      @if(request('start') || request('end') || request('fiscal_year'))
        <p class="text-muted">
          Showing packages
          @if(request('start') && request('end'))
            created from <strong>{{ \Carbon\Carbon::parse(request('start'))->format('d M Y') }}</strong>
            to <strong>{{ \Carbon\Carbon::parse(request('end'))->format('d M Y') }}</strong>
          @elseif(request('start'))
            created from <strong>{{ \Carbon\Carbon::parse(request('start'))->format('d M Y') }}</strong> onwards
          @elseif(request('end'))
            created until <strong>{{ \Carbon\Carbon::parse(request('end'))->format('d M Y') }}</strong>
          @endif
          @if(request('fiscal_year'))
            &middot; Fiscal Year <strong>{{ request('fiscal_year') }}</strong>
          @endif
        </p>
      @endif

      {{-- Table --}}
      <div class="table-responsive">
        <table id="packagesTable" class="table table-flush align-items-center mb-0" style="width:100%">
          <thead class="thead-light">
            <tr>
              <th class="text-center">Package No</th>
              <th class="text-start">Description</th>
              <th class="text-center">Procurement Method</th>
              <th class="text-center">Estimated Cost (BDT)</th>
              <th class="text-center">Assigned Officer</th>
              <th class="text-center">Fiscal Year</th>
            </tr>
          </thead>
          <tbody>
            @foreach($packages as $p)
              <tr class="text-center">
                <td class="text-center">{{ $p->package_no }}</td>
                <td class="text-start">
                  <div class="line-clamp-2" style="max-width: 460px;" title="{{ $p->description }}">
                    {{ $p->description ?? '—' }}
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge bg-gradient-info">{{ $p->procurement_method_name ?? '—' }}</span>
                </td>
                <td class="text-center" data-order="{{ $p->estimated_cost_bdt ?? 0 }}">
                  {{ number_format((float)($p->estimated_cost_bdt ?? 0), 2) }}
                </td>
                <td class="text-center">{{ $p->assigned_officer_name ?? '—' }}</td>
                <td class="text-center">{{ $p->fiscal_year ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

    </div>
  </div>
  <style>
    .line-clamp-2{
       width: 220px;
      display: -webkit-box;
      -webkit-line-clamp: 2;        /* limit to 2 lines */
      line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;              /* hide the rest */
      white-space: normal;           /* allow wrapping */
    }
  </style>
</div>

{{-- jQuery + DataTables (with Responsive) are loaded globally in the layout --}}
<script>
  $(function () {
    $('#packagesTable').DataTable({
      responsive: false,             // show all columns; no +/- collapse
      order: [[0, 'asc']],           // sort by Package No
      paging: true,                  // client-side pagination (page loads all rows)
      pageLength: 25,
      lengthMenu: [5, 10, 25, 50, 100],
      info: true,
      searching: false,              // server-side filters used instead
      columnDefs: [
        { targets: 1, className: 'text-start align-middle' },                     // Description left-aligned
        { targets: [0,2,3,4,5], className: 'text-center align-middle' }
      ],
      language: {
        emptyTable: 'No packages found.',
        zeroRecords: 'No matching packages.',
        paginate: { previous: '&laquo;', next: '&raquo;' }
      }
    });
  });
</script>
@endsection
