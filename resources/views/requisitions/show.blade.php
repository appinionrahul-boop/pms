@php use Illuminate\Support\Facades\Storage; @endphp

@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Requisition Details</h6>
      <a href="{{ route('requisitions.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to List
      </a>
    </div>

    <div class="card-body">
      {{-- Package Info --}}
      <h6 class="mb-3">Package Information</h6>
      <div class="row mb-2">
        <div class="col-md-3"><strong>Package No:</strong> {{ $requisition->package_no }}</div>
        <div class="col-md-3"><strong>ERP Requisition No:</strong> {{ $requisition->erp_requisition_no ?? '—' }}</div>
        <div class="col-md-6"><strong>Description:</strong> {{ $requisition->description }}</div>
      </div>
      <br>
      {{-- Requisition Meta --}}
      <h6 class="mt-4 mb-3">Requisition Info</h6>
      <div class="row mb-2">
        <div class="col-md-3"><strong>Status:</strong> {{ $requisition->status->name ?? '—' }}</div>
        <div class="col-md-3"><strong>Type:</strong> {{ $requisition->procurementType->name ?? '—' }}</div>
        <div class="col-md-3"><strong>Method:</strong> {{ $requisition->method->name ?? '—' }}</div>
        <div class="col-md-3"><strong>LC Status:</strong> {{ $requisition->lcStatus->name ?? '—' }}</div>
      </div>
      <div class="row mb-2">
        <div class="col-md-3"><strong>Department:</strong> {{ $requisition->department->name ?? '—' }}</div>
        <div class="col-md-3"><strong>Approving Authority:</strong> {{ $requisition->approvingAuthority->name ?? '—' }}</div>
        <div class="col-md-3"><strong>Vendor:</strong> {{ $requisition->vendor_name ?? '—' }}</div>
        <div class="col-md-3"><strong>Created:</strong> {{ $requisition->created_at->format('Y-m-d') }}</div>
      </div>

      <div class="row mb-2">
        <div class="col-md-3"><strong>Quantity:</strong> {{ (int) $requisition->quantity ?? '—' }}</div>
        <div class="col-md-3"><strong>Unit:</strong> {{ $requisition->unit->name ?? '—' }}</div>
        <div class="col-md-3"><strong>Estimated Cost:</strong> {{ number_format((float)($requisition->estimated_cost_bdt ?? 0), 2) }}</div>
        <div class="col-md-3"><strong>Official Est. Cost:</strong> {{ number_format((float)($requisition->official_estimated_cost_bdt ?? 0), 2) }}</div>
        <div class="col-md-3"><strong>Contract Amount:</strong> {{ $requisition->contract_amount !== null ? number_format((float)$requisition->contract_amount, 2) : '—' }}</div>
      </div>

      <div class="row mb-2">
        <div class="col-md-3"><strong>Receiving Date:</strong> {{ $requisition->requisition_receiving_date ?? '—' }}</div>
        <div class="col-md-3"><strong>Delivery Date:</strong> {{ $requisition->delivery_date ?? '—' }}</div>
        <div class="col-md-3"><strong>Signing Date:</strong> {{ $requisition->signing_date ?? '—' }}</div>
        <div class="col-md-3"><strong>Reference Link:</strong> 
          <!-- @if($requisition->reference_link)
            <a href="{{ $requisition->reference_link }}" target="_blank">Open</a>
          @else — @endif -->
          {{$requisition->reference_link ?? '—'}}
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-md-6"><strong>Reference Annex:</strong>
        <a href="{{ route('requisitions.annex', $requisition) }}" target="_blank" rel="noopener">Download</a> 
          <!-- @if($requisition->reference_annex)
            <a href="{{ asset('storage/'.$requisition->reference_annex) }}" target="_blank">Download</a>
          @else — @endif -->
        </div>
        <div class="col-md-6"><strong>Tech Spec File:</strong>
          @if($requisition->tech_spec_file)
            <a href="{{ asset('storage/'.$requisition->tech_spec_file) }}" target="_blank">Download</a>
          @else — @endif
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-md-12"><strong>Comments:</strong><br>{{ $requisition->comments ?? '—' }}</div>
      </div>
       <br>

      {{-- Status-wise Execution Dates --}}
      <h6 class="mt-4 mb-3">Status-wise Execution Dates</h6>
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th class="text-center" style="width:60px;">#</th>
              <th>Requisition Status</th>
              <th class="text-center">Execution Date</th>
              <th class="text-center">%</th>
            </tr>
          </thead>
          <tbody>
            @php
              // Map each status to its own dedicated execution-date column.
              $statusDateCols = [
                'Initiate'             => 'initiate_date',
                'Tender Opened'        => 'tender_opened_date',
                'Evaluation Completed' => 'evaluation_completed_date',
                'Contract Signed'      => 'signing_date',
                'Delivered'            => 'delivery_date',
              ];
              // Progress weight per status.
              $statusPctMap = [
                'Initiate'             => 20,
                'Tender Opened'        => 40,
                'Evaluation Completed' => 60,
                'Contract Signed'      => 80,
                'Delivered'            => 100,
              ];
            @endphp
            @foreach($statuses as $i => $st)
              @php
                $col      = $statusDateCols[$st->name] ?? null;
                $execDate = $col ? $requisition->{$col} : null;
                $isCurrent = ($st->id == $requisition->requisition_status_id);
                $statusPct = $statusPctMap[$st->name] ?? 0;
                $pctClass = $statusPct >= 100 ? 'bg-gradient-success'
                          : ($statusPct >= 60 ? 'bg-gradient-info'
                          : ($statusPct >= 40 ? 'bg-gradient-warning' : 'bg-gradient-secondary'));
              @endphp
              <tr class="{{ $isCurrent ? 'table-active fw-semibold' : '' }}">
                <td class="text-center">{{ $i + 1 }}</td>
                <td>
                  {{ $st->name }}
                  @if($isCurrent)
                    <span class="badge bg-gradient-primary ms-1">Current</span>
                  @endif
                </td>
                <td class="text-center">
                  {{ $execDate ? \Carbon\Carbon::parse($execDate)->format('Y-m-d') : '—' }}
                </td>
                <td class="text-center">
                  <span class="badge {{ $pctClass }}">{{ $statusPct }}%</span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <br>
      {{-- Technical Specs --}}
      <h6 class="mt-4 mb-3">Technical Specifications</h6>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th class="text-center">ERP Code</th>
              <th>Item Name</th>
                <th class="text-center">Specification</th>
              <th class="text-center">Quantity</th>
              <th class="text-center">Unit Price (BDT)</th>
              <th class="text-center">Total Price (BDT)</th>
             
            </tr>
          </thead>
          <tbody>
            @forelse($specs as $s)
              <tr>
                <td class="text-center" >{{ $s->erp_code }}</td>
                <td class="text-center" >{{ $s->spec_name }}</td>
                <td class="text-center" >{{ $s->specification }}</td>
                <td  class="text-center" >{{ (int) $s->quantity }}</td>
                <td class="text-center">{{ number_format((float)($s->unit_price_bdt ?? 0), 2) }}</td>
                <td  class="text-center" >{{ number_format((float)($s->total_price_bdt ?? 0), 2) }}</td>
                
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-secondary">No technical specs uploaded.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
