@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">

      <div class="card mb-4">
        <div class="card-header pb-0 d-flex align-items-center justify-content-between">
          <div>
            <h6 class="mb-0">APP Management — Packages</h6>
            <small class="text-muted">List of packages from your Annual Procurement Plan</small>
          </div>

          <div class="d-flex gap-2">
            {{-- Bulk Upload (Excel) --}}
            <form id="bulkUploadForm" action="{{ route('packages.bulkUpload') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input id="bulkFile" type="file" name="file" accept=".xlsx,.xls,.csv" class="d-none" onchange="document.getElementById('bulkUploadForm').submit();" />
              <button type="button" class="btn btn-outline-dark btn-sm" onclick="document.getElementById('bulkFile').click()">
                <i class="fas fa-file-upload me-1"></i> Bulk Upload (Excel)
              </button>
            </form>
            {{-- Download Sample Excel --}}
            <a href="{{ route('packages.sample') }}" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-download me-1"></i> Sample Excel
            </a>

            {{-- Add New --}}
            <a href="{{ route('packages.create') }}" class="btn bg-gradient-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add New
            </a>
          </div>
        </div>

        {{-- Filters / Search --}}
        <div class="card-body pb-0">
          <form method="GET" action="{{ route('packages.index') }}" class="row g-2">
            <div class="col-md-4">
              <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by Package No or Description">
            </div>
            <div class="col-md-2">
              <button class="btn btn-outline-secondary w-100" type="submit">
                <i class="fas fa-search me-1"></i> Search
              </button>
            </div>
            <div class="col-md-2">
             
                <a href="{{ route('packages.index') }}" class="btn btn-outline-dark w-100">Reset</a>
           
            </div>
            <div class="col-md-2">
            </div>
          </form>
        </div>

        {{-- Table --}}
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr style="text-align: center;">
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Package ID</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Package No.</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Procurement Method</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Estimated Costs (BDT)</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($packages as $pkg)
                  <tr style="text-align: center;">
                    <td><span class="text-sm">{{ $pkg->package_id }}</span></td>
                    <td><span class="text-sm fw-semibold">{{ $pkg->package_no }}</span></td>
                    <td>
                      <span class="text-sm text-truncate d-inline-block" style="max-width: 320px;">
                        {{ $pkg->description }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-gradient-info">{{ optional($pkg->method)->name ?? '—' }}</span>
                    </td>
                    <td class="text-end">
                      <span class="text-sm">{{ number_format((float)($pkg->estimated_cost_bdt ?? 0), 2) }}</span>
                    </td>
                    <td class="text-end">
                        {{-- Add Requisition (prefill with package) --}}
                        <a href="{{ url('requisitions/create?package_id='.$pkg->id) }}"
                            class="btn btn-link text-success px-2 mb-0">
                            <i class="fas fa-plus me-1"></i> Add Requisition
                        </a>

                        {{-- Edit --}}
                        <a href="{{ route('packages.edit', $pkg) }}" class="btn btn-link text-primary px-2 mb-0">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('packages.destroy', $pkg) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Delete this package? This action cannot be undone.');">
                            @csrf @method('DELETE')
                            <button class="btn btn-link text-danger px-2 mb-0" type="submit">
                            <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-4 text-sm text-secondary">
                      No packages found. Click <strong>Add New</strong> or use <strong>Bulk Upload</strong>.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- Pagination --}}
          <div class="px-3">
            {{ $packages->links() }}
          </div>
        </div>
      </div>

      {{-- Flash messages --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

    </div>
  </div>
</div>
@endsection
