@extends('layouts.user_type.auth')

@section('content')
@php
  $selStart = $start ?? null;
  $selEnd   = $end ?? null;

  // pass date range to any link that should respect the dashboard filter
  $range = [
    'date_from' => $selStart,
    'date_to'   => $selEnd,
  ];
  $pkgRange = ['start' => $selStart, 'end' => $selEnd];
@endphp

{{-- ===== Filters: Start Date & End Date ===== --}}
<form method="GET" action="{{ route('dashboard') }}" class="mb-4">
  <div class="row gx-2 gy-2 align-items-end">

    {{-- Start Date --}}
    <div class="col-12 col-md-3">
      <label class="form-label mb-1">Start Date</label>
      <input type="date" name="start" class="form-control control-eq"
             value="{{ $selStart }}">
    </div>

    {{-- End Date --}}
    <div class="col-12 col-md-3">
      <label class="form-label mb-1">End Date</label>
      <input type="date" name="end" class="form-control control-eq"
             value="{{ $selEnd }}">
    </div>

    {{-- Apply --}}
    <div class="col-6 col-md-3" style="margin-bottom:-14px">
      <label class="form-label mb-1 invisible">Apply</label>
      <button type="submit" class="btn btn-primary w-100 control-eq d-flex justify-content-center">APPLY</button>
    </div>

    {{-- Reset --}}
    <div class="col-6 col-md-3" style="margin-bottom:-14px">
      <label class="form-label mb-1 invisible">Reset</label>
      <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 control-eq d-flex justify-content-center">RESET</a>
    </div>

  </div>
</form>

{{-- Current filter label --}}
<p class="text-muted mb-4">
  Showing data for
  @if (empty($selStart) && empty($selEnd))
    <strong>All time</strong>
  @elseif (!empty($selStart) && !empty($selEnd))
    <strong>{{ \Carbon\Carbon::parse($selStart)->format('d M Y') }}</strong>
    –
    <strong>{{ \Carbon\Carbon::parse($selEnd)->format('d M Y') }}</strong>
  @elseif (!empty($selStart))
    from <strong>{{ \Carbon\Carbon::parse($selStart)->format('d M Y') }}</strong> onwards
  @elseif (!empty($selEnd))
    until <strong>{{ \Carbon\Carbon::parse($selEnd)->format('d M Y') }}</strong>
  @endif
</p>

{{-- Equalize control heights --}}
<style>
  .control-eq{
    min-height: 52px;
    font-size: 1rem;
  }
  .control-eq.form-select, .control-eq.form-control{
    padding-top: .65rem;
    padding-bottom: .65rem;
  }
</style>

{{-- Month/Year label (kept) --}}
<p class="text-muted mb-4">
  @if (empty($selYear))
    <!-- All time -->
  @elseif (empty($selMonth))
    <strong>All {{ $selYear }}</strong>
  @else
    <strong>{{ \Carbon\Carbon::create(null, (int)$selMonth, 1)->format('F') }} {{ $selYear }}</strong>
  @endif
</p>

{{-- ===== KPI Cards ===== --}}
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card p-3">
      {{-- (packages.all doesn’t take dates; leaving as-is) --}}
      <a href="{{ route('packages.all', $pkgRange) }}" class="stretched-link"></a>
      <div class="text-muted text-center">Total Packages</div>
      <div class="h4 m-0 text-center">{{ $packagesTotal }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      {{-- total requisitions respecting date range --}}
      <a href="{{ route('requisitions.index', $range) }}" class="stretched-link"></a>
      <div class="text-muted text-center">Total Requisitions</div>
      <div class="h4 m-0 text-center">{{ $requisitionsTotal }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      {{-- packages.index doesn’t support date filtering; keep link as-is --}}
      <a href="{{ route('packages.index') }}" class="stretched-link"></a>
      <div class="text-muted text-center">Packages w/o Requisition</div>
      <div class="h4 m-0 text-center">{{ $packagesWithoutReqTotal }}</div>
    </div>
  </div>
</div>

{{-- ===== 5 Status Cards (clicks preserve date range) ===== --}}
<div class="row g-3 mb-4">
  {{-- Initiate --}}
  <div class="col-md-2">
    <div class="card p-3 text-center position-relative">
      <a href="{{ route('requisitions.index',
            array_merge($range, ['status_id' => $statusIds['Initiate'] ?? null])) }}"
         class="stretched-link"></a>
      <div class="text-muted small text-center">Initiate</div>
      <div class="h4 m-0 text-center">{{ $initiateCount }}</div>
    </div>
  </div>

  {{-- Tender Opened --}}
  <div class="col-md-3">
    <div class="card p-3 text-center position-relative">
      <a href="{{ route('requisitions.index',
            array_merge($range, ['status_id' => $statusIds['Tender Opened'] ?? null])) }}"
         class="stretched-link"></a>
      <div class="text-muted small text-center">Tender Opened</div>
      <div class="h4 m-0 text-center">{{ $tenderOpenedCount }}</div>
    </div>
  </div>

  {{-- Evaluation Completed --}}
  <div class="col-md-3">
    <div class="card p-3 text-center position-relative">
      <a href="{{ route('requisitions.index',
            array_merge($range, ['status_id' => $statusIds['Evaluation Completed'] ?? null])) }}"
         class="stretched-link"></a>
      <div class="text-muted small text-center">Evaluation Completed</div>
      <div class="h4 m-0 text-center">{{ $evaluationCount }}</div>
    </div>
  </div>

  {{-- Contract Signed --}}
  <div class="col-md-2">
    <div class="card p-3 text-center position-relative">
      <a href="{{ route('requisitions.index',
            array_merge($range, ['status_id' => $statusIds['Contract Signed'] ?? null])) }}"
         class="stretched-link"></a>
      <div class="text-muted small text-center">Contract Signed</div>
      <div class="h4 m-0 text-center">{{ $contractSignedCount }}</div>
    </div>
  </div>

  {{-- Delivered --}}
  <div class="col-md-2">
    <div class="card p-3 text-center position-relative">
      <a href="{{ route('requisitions.index',
            array_merge($range, ['status_id' => $statusIds['Delivered'] ?? null])) }}"
         class="stretched-link"></a>
      <div class="text-muted small text-center">Delivered</div>
      <div class="h4 m-0 text-center">{{ $deliveredCount }}</div>
    </div>
  </div>
</div>

{{-- ===== Department & Procurement Type Wise Requisitions ===== --}}
<div class="row justify-content-center">
  {{-- Department Wise --}}
  <div class="col-md-6">
    <div class="card mb-4">
      <div class="card-header text-center">Requisitions by Department</div>
      <div class="card-body p-0">
        <table class="table table-sm table-striped align-middle mb-0">
          <thead class="table-light">
            <tr class="align-middle">
              <th class="text-center">Department</th>
              <th class="text-center">Requisitions</th>
            </tr>
          </thead>
          <tbody class="align-middle">
            @forelse($departmentCounts as $row)
              <tr class="align-middle">
                <td class="text-center">{{ $row->name }}</td>
                <td class="text-center">{{ $row->total }}</td>
              </tr>
            @empty
              <tr class="align-middle">
                <td colspan="2" class="text-center text-muted">No data available.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Procurement Type Wise --}}
  <div class="col-md-6">
    <div class="card mb-4">
      <div class="card-header text-center">Requisitions by Procurement Type</div>
      <div class="card-body p-0">
        <table class="table table-sm table-striped align-middle mb-0">
          <thead class="table-light">
            <tr class="align-middle">
              <th class="text-center">Procurement Type</th>
              <th class="text-center">Requisitions</th>
            </tr>
          </thead>
          <tbody class="align-middle">
            @forelse($typeCounts as $row)
              <tr class="align-middle">
                <td class="text-center">{{ $row->name }}</td>
                <td class="text-center">{{ $row->total }}</td>
              </tr>
            @empty
              <tr class="align-middle">
                <td colspan="2" class="text-center text-muted">No data available.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Equalize control heights (selects only) --}}
<style>
  .control-eq{
    min-height: 52px;   /* larger, consistent height for selects & buttons */
    font-size: 1rem;
  }
  .control-eq.form-select{
    padding-top: .65rem;
    padding-bottom: .65rem;
  }
</style>
@endsection
