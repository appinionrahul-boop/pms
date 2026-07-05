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
          {{-- Keyword Search --}}
          <div class="col-md-3">
            <label for="k" class="form-label">Keyword</label>
            <input type="text" id="k" name="k" class="form-control"
                  value="{{ $filters['k'] ?? '' }}"
                  placeholder="Search (Package No / Vendor / Description)">
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
            <a href="{{ route('requisitions.index') }}" class="btn btn-outline-dark w-100">Reset</a>
          </div>
        </form>


      {{-- Table --}}
      <div class="table-responsive">
        <table id="packagesTable" class="table table-flush align-items-center mb-0" style="width:100%">
          <thead class="thead-light">
            <tr>
              <th class="text-center" data-priority="3">ERP Req. No.</th>
              <th class="text-center" data-priority="1">Package No</th>
              <th class="text-start">Description</th>
              <th class="text-center">Procurement Method</th>
              <th class="text-center" data-priority="2">Requisition Status</th>
              <th class="text-center">Name of Vendor</th>
              <th class="text-center">Department</th>
              <th class="text-center">Type of Procurement</th>
              <th class="text-center">Assigned Officer</th>
              <th class="text-center" data-priority="3">% Status</th>
              <th class="text-center" data-priority="1">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($requisitions as $r)
              <tr class="text-center">
                <td class="text-center">{{ $r->erp_requisition_no ?? '—' }}</td>
                <td class="text-center">{{ $r->package_no ?? '—' }}</td>
                <td class="text-start">
                   <div class="line-clamp-2" style="max-width: 460px;" title="{{ $r->description }}">
                  {{ $r->description ?? '—' }}
                </div>
               </td>
                <td class="text-center ">
                   <span class="badge bg-gradient-info">  {{ $r->method->name ?? '—' }}</span>
    
                
                </td>
                <td class="text-center">
                 <span class="badge bg-gradient-secondary" role="button" style="cursor:pointer;"
                       data-bs-toggle="modal" data-bs-target="#execModal{{ $r->id }}"
                       title="View status-wise execution dates">
                   {{ $r->status->name ?? '—' }} <span class="ms-1" style="font-size:.85em;">&#128065;</span>
                 </span>
               </td>
                <td class="text-center">{{ $r->vendor_name ?? '—' }}</td>
                <td class="text-center">{{ $r->department->name ?? '—' }}</td>
                <td class="text-center">{{ $r->procurementType->name ?? '—' }}</td>
                <td class="text-center">{{ $r->officer_name ?? '—' }}</td>
                @php
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

      {{-- Status-wise Execution Dates modals (one per requisition) --}}
      @foreach($requisitions as $r)
        @php
          $execStatuses = [
            ['name' => 'Initiate',             'col' => 'initiate_date',             'pct' => 20],
            ['name' => 'Tender Opened',        'col' => 'tender_opened_date',        'pct' => 40],
            ['name' => 'Evaluation Completed', 'col' => 'evaluation_completed_date', 'pct' => 60],
            ['name' => 'Contract Signed',      'col' => 'signing_date',              'pct' => 80],
            ['name' => 'Delivered',            'col' => 'delivery_date',             'pct' => 100],
          ];
          $curName = $r->status->name ?? null;
        @endphp
        <div class="modal fade" id="execModal{{ $r->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h6 class="modal-title mb-0">
                  Status-wise Execution Dates
                  <small class="text-muted">— {{ $r->erp_requisition_no ?: ('Req #'.$r->id) }}</small>
                </h6>
                <button type="button" class="btn btn-link text-dark p-0 border-0 fw-bold"
                        data-bs-dismiss="modal" aria-label="Close"
                        style="font-size:1.5rem; line-height:1; cursor:pointer; text-decoration:none;">
                  &times;
                </button>
              </div>
              <div class="modal-body">
                <table class="table table-bordered align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="text-center" style="width:60px;">#</th>
                      <th>Requisition Status</th>
                      <th class="text-center">Execution Date</th>
                      <th class="text-center">%</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($execStatuses as $i => $st)
                      @php
                        $execDate  = $r->{$st['col']} ?? null;
                        $isCurrent = ($curName === $st['name']);
                        $p  = $st['pct'];
                        $pc = $p >= 100 ? 'bg-gradient-success'
                            : ($p >= 60 ? 'bg-gradient-info'
                            : ($p >= 40 ? 'bg-gradient-warning' : 'bg-gradient-secondary'));
                      @endphp
                      <tr class="{{ $isCurrent ? 'table-active fw-semibold' : '' }}">
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>
                          {{ $st['name'] }}
                          @if($isCurrent)<span class="badge bg-gradient-primary ms-1">Current</span>@endif
                        </td>
                        <td class="text-center">
                          {{ $execDate ? \Carbon\Carbon::parse($execDate)->format('Y-m-d') : '—' }}
                        </td>
                        <td class="text-center"><span class="badge {{ $pc }}">{{ $p }}%</span></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      @endforeach

      {{-- Server-side pagination --}}
      <div class="mt-3">
        {{ $requisitions->links() }}
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
      order: [[0, 'asc']],           // sort by Package No
      paging: false,                 // server-side pagination (Laravel links below the table)
      info: false,
      searching: false,              // search bar hidden (you have server-side filters)
      columnDefs: [
        { targets: 10, orderable: false, className: 'text-center align-middle' }, // Actions column
        { targets: 2, className: 'text-start align-middle' },                      // Description left-aligned
        { targets: [0,1,3,4,5,6,7,8,9], className: 'text-center align-middle' }
      ],
      language: {
        emptyTable: 'No requisitions found.',
        zeroRecords: 'No matching requisitions.'
      }
    });
  });
</script>
@endsection
