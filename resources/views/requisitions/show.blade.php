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
        <div class="col-md-3"><strong>Package ID:</strong> {{ $requisition->package->package_id ?? '—' }}</div>
        <div class="col-md-3"><strong>Package No:</strong> {{ $requisition->package_no }}</div>
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
        <div class="col-md-3"><strong>Quantity:</strong> {{ $requisition->quantity ?? '—' }}</div>
        <div class="col-md-3"><strong>Unit:</strong> {{ $requisition->unit->name ?? '—' }}</div>
        <div class="col-md-3"><strong>Estimated Cost:</strong> {{ number_format((float)($requisition->estimated_cost_bdt ?? 0), 2) }}</div>
        <div class="col-md-3"><strong>Official Est. Cost:</strong> {{ number_format((float)($requisition->official_estimated_cost_bdt ?? 0), 2) }}</div>
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
          @if($requisition->reference_annex)
            <a href="{{ asset('storage/'.$requisition->reference_annex) }}" target="_blank">Download</a>
          @else — @endif
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
      {{-- Technical Specs --}}
      <h6 class="mt-4 mb-3">Technical Specifications</h6>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Spec Name</th>
              <th class="text-end">Quantity</th>
              <th class="text-end">Unit Price (BDT)</th>
              <th class="text-end">Total Price (BDT)</th>
              <th class="text-end">ERP Code</th>
            </tr>
          </thead>
          <tbody>
            @forelse($specs as $s)
              <tr>
                <td>{{ $s->spec_name }}</td>
                <td class="text-end">{{ $s->quantity }}</td>
                <td class="text-end">{{ number_format((float)($s->unit_price_bdt ?? 0), 2) }}</td>
                <td class="text-end">{{ number_format((float)($s->total_price_bdt ?? 0), 2) }}</td>
                <td>{{ $s->erp_code }}</td>
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
