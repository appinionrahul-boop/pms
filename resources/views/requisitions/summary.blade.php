@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <div>
        <h6 class="mb-0">Requisition Summary</h6>
      </div>
    </div>

    <div class="card-body pt-3">

      {{-- Filters --}}
      <form method="GET" action="{{ route('requisitions.summary') }}" class="row g-2 mb-3">
          {{-- Keyword Search --}}
          <div class="col-md-3">
            <label for="k" class="form-label">Keyword</label>
            <input type="text" id="k" name="k" class="form-control"
                  value="{{ $filters['k'] ?? '' }}"
                  placeholder="Search (Package No / ERP / Vendor / Description)">
          </div>

          {{-- Status --}}
          <div class="col-md-2">
            <label for="status_id" class="form-label">Requisition Status</label>
            <select id="status_id" name="status_id" class="form-control">
              <option value="">All</option>
              @foreach($statuses as $s)
                <option value="{{ $s->id }}" @selected(($filters['status_id'] ?? null) == $s->id)>{{ $s->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Procurement Type --}}
          <div class="col-md-2">
            <label for="procurement_type_id" class="form-label">Procurement Type</label>
            <select id="procurement_type_id" name="procurement_type_id" class="form-control">
              <option value="">All</option>
              @foreach($types as $t)
                <option value="{{ $t->id }}" @selected(($filters['procurement_type_id'] ?? null) == $t->id)>{{ $t->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Procurement Method --}}
          <div class="col-md-2">
            <label for="procurement_method_id" class="form-label">Procurement Method</label>
            <select id="procurement_method_id" name="procurement_method_id" class="form-control">
              <option value="">All</option>
              @foreach($methods as $m)
                <option value="{{ $m->id }}" @selected(($filters['procurement_method_id'] ?? null) == $m->id)>{{ $m->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- LC Status --}}
          <div class="col-md-2">
            <label for="lc_status_id" class="form-label">LC Status</label>
            <select id="lc_status_id" name="lc_status_id" class="form-control">
              <option value="">All</option>
              @foreach($lcStatuses as $l)
                <option value="{{ $l->id }}" @selected(($filters['lc_status_id'] ?? null) == $l->id)>{{ $l->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Assigned Person --}}
          <div class="col-md-2">
            <label for="officer_name" class="form-label">Assigned Person</label>
            <select id="officer_name" name="officer_name" class="form-control">
              <option value="">All</option>
              @foreach($officers as $off)
                <option value="{{ $off->name }}" @selected(($filters['officer_name'] ?? null) == $off->name)>{{ $off->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Start Date --}}
          <div class="col-md-2">
            <label for="date_from" class="form-label">Start Date</label>
            <input type="date" id="date_from" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
          </div>

          {{-- End Date --}}
          <div class="col-md-2">
            <label for="date_to" class="form-label">End Date</label>
            <input type="date" id="date_to" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
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
            <a href="{{ route('requisitions.summary') }}" class="btn btn-outline-dark w-100">Reset</a>
          </div>
        </form>


      {{-- Table --}}
      <div class="table-responsive">
        <table id="summaryTable" class="table table-flush align-items-center mb-0" style="width:100%">
          <thead class="thead-light">
            <tr>
              <th class="text-center">ERP Code</th>
              <th class="text-center">Description</th>
              <th class="text-center">Requisition Status</th>
              <th class="text-center">Assigned Officer</th>
              <th class="text-center">% Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($requisitions as $r)
              <tr class="text-center">
                <td class="text-center">{{ $r->erp_requisition_no ?? '—' }}</td>
                <td>
                  <div class="line-clamp-2 mx-auto" style="max-width: 460px;" title="{{ $r->description }}">
                    {{ $r->description ?? '—' }}
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge bg-gradient-secondary">{{ $r->status->name ?? '—' }}</span>
                </td>
                <td class="text-center">{{ $r->officer_name ?? '—' }}</td>
                @php
                  // Progress based on the current requisition status.
                  $statusPctMap = [
                    'Initiate'             => 20,
                    'Tender Opened'        => 40,
                    'Evaluation Completed' => 60,
                    'Contract Signed'      => 80,
                    'Delivered'            => 100,
                  ];
                  $statusPct = $statusPctMap[$r->status->name ?? ''] ?? 0;
                  $pctClass = $statusPct >= 100 ? 'bg-gradient-success'
                            : ($statusPct >= 60 ? 'bg-gradient-info'
                            : ($statusPct >= 40 ? 'bg-gradient-warning' : 'bg-gradient-secondary'));
                @endphp
                <td class="text-center" data-order="{{ $statusPct }}">
                  <span class="badge {{ $pctClass }}">{{ $statusPct }}%</span>
                </td>
                <td class="text-center">
                  <a href="{{ route('requisitions.show', $r) }}" class="btn btn-link text-secondary px-2 mb-0">
                    <i class="fas fa-eye me-1"></i> View
                  </a>
                </td>
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
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      white-space: normal;
    }
  </style>
</div>

{{-- jQuery + DataTables CDN --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script>
  $(function () {
    $('#summaryTable').DataTable({
      order: [[0, 'asc']],            // sort by ERP Code
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      searching: false,               // server-side filters used instead
      columnDefs: [
        { targets: 5, orderable: false, className: 'text-center align-middle' }, // Actions
        { targets: [0,1,2,3,4], className: 'text-center align-middle' }
      ],
      language: {
        emptyTable: 'No requisitions found.',
        zeroRecords: 'No matching requisitions.'
      }
    });
  });
</script>
@endsection
