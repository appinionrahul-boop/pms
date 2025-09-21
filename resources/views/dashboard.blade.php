@extends('layouts.user_type.auth')

@section('content')

@php
  // Month labels for the dropdown
  $months = [
    1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
    7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'
  ];

  // Selected values coming from controller; when lifetime, they may be null
  $selMonth = $month ?? null;  // null = All Months
  $selYear  = $year  ?? null;  // null = All time
@endphp

{{-- ===== Filters: Year (required), Month (optional) ===== --}}
<form method="GET" action="{{ route('dashboard') }}" class="mb-4">
  <div class="row gx-2 gy-2 align-items-end">

    {{-- Month (optional) --}}
    <div class="col-12 col-md-3">
      <label class="form-label mb-1 text-center">Month</label>
      <select name="month" class="form-select control-eq">
        <option value="" {{ empty($selMonth) ? 'selected' : '' }}>All Months</option>
        @foreach ($months as $mVal => $mLabel)
          <option value="{{ $mVal }}" @selected((int)$selMonth === (int)$mVal)>{{ $mLabel }}</option>
        @endforeach
      </select>
    </div>

    {{-- Year (required, 2025–2028) --}}
    <div class="col-12 col-md-3">
      <label class="form-label mb-1">Year</label>
      <select name="year" class="form-select control-eq" required>
        @foreach (($allowedYears ?? [2025, 2026, 2027, 2028]) as $y)
          <option value="{{ $y }}" @selected((int)$selYear === (int)$y)>{{ $y }}</option>
        @endforeach
      </select>
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
  @if (empty($selYear))
    <strong>All time</strong>
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
       <a href="{{ route('packages.all') }}" class="stretched-link"></a>
      <div class="text-muted text-center">Total Packages</div>
      <div class="h4 m-0 text-center">{{ $packagesTotal }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
       <a href="{{ route('requisitions.index') }}" class="stretched-link"></a>
      <div class="text-muted text-center">Total Requisitions</div>
      <div class="h4 m-0 text-center">{{ $requisitionsTotal }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <a href="{{ route('packages.index') }}" class="stretched-link"></a>
      <div class="text-muted text-center">Packages w/o Requisition</div>
      <div class="h4 m-0 text-center">{{ $packagesWithoutReqTotal }}</div>
    </div>
  </div>
</div>

{{-- ===== 5 Status Cards ===== --}}
<div class="row g-3 mb-4">
  <div class="col-md-2">
    <div class="card p-3 text-center">
      <div class="text-muted small text-center">Initiate</div>
      <div class="h4 m-0 text-center">{{ $initiateCount }}</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 text-center">
      <div class="text-muted small text-center">Tender Opened</div>
      <div class="h4 m-0 text-center">{{ $tenderOpenedCount }}</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 text-center">
      <div class="text-muted small text-center">Evaluation Completed</div>
      <div class="h4 m-0 text-center">{{ $evaluationCount }}</div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card p-3 text-center">
      <div class="text-muted small text-center">Contract Signed</div>
      <div class="h4 m-0 text-center">{{ $contractSignedCount }}</div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card p-3 text-center">
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

{{-- Equalize control heights --}}
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
