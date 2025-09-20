<table border="1" cellspacing="0" cellpadding="5">
  <thead style="background:#f0f0f0; font-weight:bold;">
    <tr>
      <th>Package ID</th>
      <th>Package No.</th>
      <th>Description</th>
      <th>Procurement Method</th>
      <th>Requisition Status</th>
      <th>Name of Vendor</th>
      <th>Department</th>
      <th>Type of Procurement</th>
      <th>LC Status</th>
      <th>Assigned Officer</th>
      <th>Unit</th>
      <th class="text-end">Estimated Cost (BDT)</th>
      <th>Quantity/Nos.</th>
      <th>Approving Authority</th>
      <th>Signing Date</th>
      <th class="text-end">Official Est. Cost (BDT)</th>
      <th>Requisition Receiving Date</th>
      <th>Delivery Date</th>
      <th>Reference Link</th>
      <!-- <th>Created</th> -->
    </tr>
  </thead>

  <tbody>
  @foreach($records as $r)
    <tr>
      <td>{{ $r->package_id }}</td>
      <td>{{ $r->package_no }}</td>
      <td style="max-width:420px">{{ $r->description }}</td>

      {{-- Procurement Method / Status / Vendor / Department / Type / LC --}}
      <td>{{ $r->procurement_method ?? '—' }}</td>
      <td>{{ $r->requisition_status ?? '—' }}</td>
      <td>{{ $r->vendor_name ?? '—' }}</td>
      <td>{{ $r->department ?? '—' }}</td>
      <td>{{ $r->procurement_type ?? '—' }}</td>
      <td>{{ $r->lc_status ?? '—' }}</td>

      {{-- Assigned Officer (either free-text officer_name or joined user name) --}}
      <td>{{ $r->officer_name ?: ($r->assigned_officer ?? '—') }}</td>

      {{-- Unit --}}
      <td>{{ $r->unit ?? '—' }}</td>

      {{-- Estimated Cost (BDT) --}}
      <td class="text-end">
        {{ is_null($r->estimated_cost_bdt) ? '—' : number_format((float)$r->estimated_cost_bdt, 2) }}
      </td>

      {{-- Quantity / Nos. --}}
      <td>{{ $r->quantity ?? '—' }}</td>

      {{-- Approving Authority --}}
      <td>{{ $r->approving_authority ?? '—' }}</td>

      {{-- Signing Date --}}
      <td>
        {{ $r->signing_date ? \Carbon\Carbon::parse($r->signing_date)->format('d M Y') : '—' }}
      </td>

      {{-- Official Estimated Cost (BDT) --}}
      <td class="text-end">
        {{ is_null($r->official_estimated_cost_bdt) ? '—' : number_format((float)$r->official_estimated_cost_bdt, 2) }}
      </td>

      {{-- Requisition Receiving Date --}}
      <td>
        {{ $r->requisition_receiving_date ? \Carbon\Carbon::parse($r->requisition_receiving_date)->format('d M Y') : '—' }}
      </td>

      {{-- Delivery Date --}}
      <td>
        {{ $r->delivery_date ? \Carbon\Carbon::parse($r->delivery_date)->format('d M Y') : '—' }}
      </td>

      {{-- Reference Link --}}
      <td>
        <!-- @if(!empty($r->reference_link))
          <a href="{{ $r->reference_link }}" target="_blank" rel="noopener"></a>
        @else
          —
        @endif -->
       {{$r->reference_link ?? '—'}} 
      </td>

      {{-- Created (came from select as plain value, so parse) --}}
      <!-- <td>
        {{ $r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('d M Y') : '—' }}
      </td> -->
    </tr>
  @endforeach
</tbody>


</table>
