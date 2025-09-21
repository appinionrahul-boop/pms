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
            <th class="text-center">Package ID</th>
            <th class="text-center">Package No.</th>
            <th class="text-center">Description</th>
            <th class="text-center">Procurement Method</th>
            <th class="text-center">Requisition Status</th>
            <th class="text-center">Name of Vendor</th>
            <th class="text-center">Department</th>
            <th class="text-center">Type of Procurement</th>
            <th class="text-center">LC Status</th>
            <th class="text-center">Assigned Officer</th>
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
              <td class="text-center">{{ $r->package_id }}</td>
              <td class="text-center">{{ $r->package_no }}</td>
              <td class="text-center" style="max-width:420px">{{ $r->description }}</td>
              <td class="text-center">{{ $r->procurement_method ?? '—' }}</td>
              <td class="text-center">
                <span class="badge bg-secondary">{{ $r->requisition_status ?? '—' }}</span>
              </td>
              <td class="text-center">{{ $r->vendor_name ?? '—' }}</td>
              <td class="text-center">{{ $r->department ?? '—' }}</td>
              <td class="text-center">{{ $r->procurement_type ?? '—' }}</td>
              <td class="text-center">{{ $r->lc_status ?? '—' }}</td>
              <td class="text-center">{{ $r->officer_name ?? '—' }}</td>
              <td class="text-center">{{ $r->unit ?? '—' }}</td>

              {{-- INTEGER formatting for money/qty --}}
              <td class="text-end">
                @if(isset($r->estimated_cost_bdt) && is_numeric($r->estimated_cost_bdt))
                  {{ number_format((int)$r->estimated_cost_bdt, 0) }}
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
                  {{ number_format((int)$r->official_estimated_cost_bdt, 0) }}
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
</div>

{{-- jQuery + DataTables CDN --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script>
  // Avoid DataTables alert popups
  $.fn.dataTable.ext.errMode = 'none';

  $(function () {
    $('#packagesTable').DataTable({
      order: [[1, 'asc']],  // sort by Package No by default
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [
        { targets: '_all', className: 'align-middle' },
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
