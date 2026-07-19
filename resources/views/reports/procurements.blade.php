@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid">
  <h4 class="mb-3">Procurement Report</h4>

  {{-- Filters --}}
  <form method="GET" action="{{ route('reports.procurements') }}" class="card p-3 mb-4">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Requisition Status</label>
        <select name="requisition_status_id" class="form-select">
          <option value="">All</option>
          @foreach($reqStatuses as $k => $v)
            <option value="{{ $k }}" {{ request('requisition_status_id')==$k ? 'selected' : '' }}>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Procurement Type</label>
        <select name="procurement_type_id" class="form-select">
          <option value="">All</option>
          @foreach($procurementTypes as $k => $v)
            <option value="{{ $k }}" {{ request('procurement_type_id')==$k ? 'selected' : '' }}>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Method</label>
        <select name="procurement_method_id" class="form-select">
          <option value="">All</option>
          @foreach($methods as $k => $v)
            <option value="{{ $k }}" {{ request('procurement_method_id')==$k ? 'selected' : '' }}>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">LC Status</label>
        <select name="lc_status_id" class="form-select">
          <option value="">All</option>
          @foreach($lcStatuses as $k => $v)
            <option value="{{ $k }}" {{ request('lc_status_id')==$k ? 'selected' : '' }}>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Department</label>
        <select name="department_id" class="form-select">
          <option value="">All</option>
          @foreach($departments as $id => $name)
            <option value="{{ $id }}" {{ request('department_id')==$id ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Assigned Person</label>
        <select name="officer_name" class="form-select">
          <option value="">All</option>
          @foreach($officers as $officer)
            <option value="{{ $officer }}" {{ request('officer_name')==$officer ? 'selected' : '' }}>{{ $officer }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Date (Start)</label>
        <input type="date" name="date_start" class="form-control" value="{{ request('date_start') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Date (End)</label>
        <input type="date" name="date_end" class="form-control" value="{{ request('date_end') }}">
      </div>
    </div>

    <div class="d-flex gap-2 mt-3">
      <button class="btn btn-primary" type="submit">Apply Search</button>
      <a href="{{ route('reports.procurements') }}" class="btn btn-outline-secondary">Reset</a>
      <a href="{{ route('reports.procurements.export', request()->query()) }}" class="btn btn-outline-success">Export Excel</a>
    </div>
  </form>

  {{-- Results --}}
  <div class="card">
    <div class="table-responsive">
      <table id="packagesTable" class="table table-striped table-hover align-middle">
        <thead>
          <tr>
            <th class="text-center" data-priority="2">ERP Req. No.</th>
            <th class="text-center" data-priority="1">Package No.</th>
            <th class="text-start">Description</th>
            <th class="text-center">Procurement Method</th>
            <th class="text-center" data-priority="2">Requisition Status</th>
            <th class="text-center">Name of Vendor</th>
            <th class="text-center">Department</th>
            <th class="text-center">Type of Procurement</th>
            <th class="text-center">LC Status</th>
            <th class="text-start">Assigned Person</th>
            <th class="text-center">Unit</th>
            <th class="text-center">Estimated Cost (BDT)</th>
            <th class="text-center">Quantity/Nos.</th>
            <th class="text-center">Approving Authority</th>
            <th class="text-center">Signing Date</th>
            <th class="text-center">Official Est. Cost (BDT)</th>
            <th class="text-center">Requisition Receiving Date</th>
            <th class="text-center">Delivery Date</th>
            <th class="text-center">Reference Link</th>
          </tr>
        </thead>
        <tbody>
          @foreach($records as $r)
            <tr>
              <td class="text-center">{{ $r->erp_requisition_no ?? '—' }}</td>
              <td class="text-center">{{ $r->package_no }}</td>
              <td class="text-start">
                <div class="line-clamp-2" style="max-width: 460px;" title="{{ $r->description }}">
                  {{ $r->description }}
                </div>
              </td>
              <td class="text-center">{{ $r->procurement_method ?? '—' }}</td>
              <td class="text-center">
                <span class="badge bg-gradient-secondary" role="button" style="cursor:pointer;"
                      data-bs-toggle="modal" data-bs-target="#execModal{{ $r->id }}"
                      title="View status-wise execution dates">
                  {{ $r->requisition_status ?? '—' }} <span class="ms-1" style="font-size:.85em;">&#128065;</span>
                </span>
              </td>
              <td class="text-center">{{ $r->vendor_name ?? '—' }}</td>
              <td class="text-center">{{ $r->department ?? '—' }}</td>
              <td class="text-center">{{ $r->procurement_type ?? '—' }}</td>
              <td class="text-center">{{ $r->lc_status ?? '—' }}</td>
              <td class="text-start">{{ $r->officer_name ?? '—' }}</td>
              <td class="text-center">{{ $r->unit ?? '—' }}</td>

              {{-- INTEGER formatting for money/qty --}}
              <td class="text-end">
                @if(isset($r->estimated_cost_bdt) && is_numeric($r->estimated_cost_bdt))
                  {{ $r->estimated_cost_bdt, 0 }}
                @else
                  —
                @endif
              </td>
              <td class="text-center">
                @if(isset($r->quantity) && is_numeric($r->quantity))
                  {{ (int)$r->quantity }}
                @else
                  —
                @endif
              </td>
              <td class="text-center">{{ $r->approving_authority ?? '—' }}</td>
              <td class="text-center">
                {{ $r->signing_date ? \Carbon\Carbon::parse($r->signing_date)->format('d M Y') : '—' }}
              </td>
              <td class="text-end">
                @if(isset($r->official_estimated_cost_bdt) && is_numeric($r->official_estimated_cost_bdt))
                  {{ $r->official_estimated_cost_bdt, 0}}
                @else
                  —
                @endif
              </td>
              <td class="text-center">
                {{ $r->requisition_receiving_date ? \Carbon\Carbon::parse($r->requisition_receiving_date)->format('d M Y') : '—' }}
              </td>
              <td class="text-center">
                {{ $r->delivery_date ? \Carbon\Carbon::parse($r->delivery_date)->format('d M Y') : '—' }}
              </td>
              <td class="text-center">
                {{ $r->reference_link ?? '—' }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="card-footer">
      {{ $records->links() }}
    </div>
  </div>

  {{-- Status-wise Execution Dates modals (one per record) --}}
  @foreach($records as $r)
    @php
      $execStatuses = [
        ['name' => 'Initiate',             'col' => 'initiate_date',             'pct' => 20],
        ['name' => 'Tender Opened',        'col' => 'tender_opened_date',        'pct' => 40],
        ['name' => 'Evaluation Completed', 'col' => 'evaluation_completed_date', 'pct' => 60],
        ['name' => 'Contract Signed',      'col' => 'signing_date',              'pct' => 80],
        ['name' => 'Delivered',            'col' => 'delivery_date',             'pct' => 100],
      ];
      $curName = $r->requisition_status ?? null;
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
  <style>
    .line-clamp-2{
       width: 220px;  
      display: -webkit-box;
      -webkit-line-clamp: 3;        /* limit to 2 lines */
      -webkit-box-orient: vertical;
      overflow: hidden;              /* hide the rest */
      white-space: normal;           /* allow wrapping */
    }
  </style>
</div>

{{-- jQuery + DataTables (with Responsive) are loaded globally in the layout --}}
<script>
  // Avoid DataTables alert popups
  $.fn.dataTable.ext.errMode = 'none';

  $(function () {
    $('#packagesTable').DataTable({
      order: [[1, 'asc']],  // sort by Package No by default
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      searching:false,
      columnDefs: [
        { targets: '_all', className: 'align-middle' },
        // Description & Assigned Person left-aligned
        { targets: [2, 9], className: 'text-start align-middle' },
        // Make currency columns align right for readability
        { targets: [11, 15], className: 'text-end align-middle' }
      ],
      language: {
        search: 'Search:',
        searchPlaceholder: 'Type to filter...',
        emptyTable: 'No data found.',
        zeroRecords: 'No matching records for the applied filters.'
      }
    });
  });
</script>
@endsection
