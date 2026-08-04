@extends('layouts.user_type.auth')

@section('content')

<style>
  /* keep option text clear of the dropdown caret */
  select.form-control, select.form-select{
    padding-right: 2.25rem !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .pkg-desc { text-align: left !important; }
  .pkg-desc-text{
    display: inline-block;
    max-width: 320px;          /* adjust as needed */
    white-space: normal;
    word-break: break-word;
    overflow-wrap: break-word;
    text-align: justify;       /* justify lines */
    text-justify: inter-word;  /* better spacing on supported browsers */
    hyphens: auto;             /* nicer breaks for long words */
  }
  /* make selection checkboxes clearly visible over the theme defaults */
  #selectAllPackages, .pkg-select{
    width: 1.15rem;
    height: 1.15rem;
    border: 2px solid #67748e !important;
    border-radius: 0.25rem;
    background-color: #fff;
    cursor: pointer;
    vertical-align: middle;
    margin: 0;
  }
  #selectAllPackages:checked, .pkg-select:checked{
    background-color: #ea0606 !important;
    border-color: #ea0606 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3 6-6'/%3e%3c/svg%3e") !important;
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
  }
  #selectAllPackages:indeterminate{
    background-color: #ea0606 !important;
    border-color: #ea0606 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-width='3' d='M5 10h10'/%3e%3c/svg%3e") !important;
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
  }
</style>


<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">

      {{-- Flash messages --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if(session('warnings'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Imported, but check these:</strong>
          <ul class="mb-0 mt-2 ps-3">
            @foreach(session('warnings') as $warning)
              <li class="text-sm">{{ $warning }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Bulk upload failed — nothing was imported.</strong>
          <ul class="mb-0 mt-2 ps-3">
            @foreach($errors->all() as $error)
              <li class="text-sm">{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <div class="card mb-4">
        <div class="card-header pb-0 d-flex align-items-center justify-content-between">
          <div>
            <h6 class="mb-0">APP Management — Packages</h6>
            <small class="text-muted">List of packages from your Annual Procurement Plan</small>
          </div>

          <div class="d-flex gap-2">
            {{-- Delete Selected --}}
            <button type="button" id="bulkDeleteBtn" class="btn btn-outline-danger btn-sm d-none">
              <i class="fas fa-trash me-1"></i> Delete Selected (<span id="bulkDeleteCount">0</span>)
            </button>

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
              <select name="officer_id" class="form-control">
                <option value="">All Officers</option>
                @foreach($officers as $officer)
                  <option value="{{ $officer->id }}" @selected(request('officer_id') == $officer->id)>{{ $officer->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <select name="fiscal_year" class="form-control">
                <option value="">All Fiscal Years</option>
                @foreach($fiscalYears as $fy)
                  <option value="{{ $fy }}" @selected(request('fiscal_year') === $fy)>{{ $fy }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <button class="btn btn-outline-secondary w-100" type="submit">
                <i class="fas fa-search me-1"></i> Search
              </button>
            </div>
            <div class="col-md-2">

                <a href="{{ route('packages.index') }}" class="btn btn-outline-dark w-100">Reset</a>

            </div>
          </form>
        </div>

        {{-- Table --}}
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table id="appPackagesTable" class="table align-items-center mb-0" style="width:100%">
              <thead>
                <tr style="text-align: center;">
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width:40px;">
                    <input type="checkbox" id="selectAllPackages" class="form-check-input" title="Select all on this page">
                  </th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Package No.</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Procurement Method</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Estimated Costs (BDT)</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Assigned Officer</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fiscal Year</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($packages as $pkg)
                  <tr style="text-align: center;">
                    <td>
                      <input type="checkbox" class="form-check-input pkg-select" value="{{ $pkg->id }}">
                    </td>
                    <td><span class="text-sm fw-semibold">{{ $pkg->package_no }}</span></td>
                    <!--<td>-->
                    <!--  <span class="text-sm text-truncate d-inline-block" style="max-width: 320px;">-->
                    <!--    {{ $pkg->description }}-->
                    <!--  </span>-->
                    <!--</td>-->
                    <!--<td>-->
                    <!--  @php $desc = trim((string)($pkg->description ?? '')); @endphp-->
                    <!--  @if($desc !== '')-->
                    <!--    <button type="button"-->
                    <!--            class="btn btn-link p-0 text-decoration-underline desc-trigger text-sm"-->
                    <!--            data-desc="{{ e($desc) }}"-->
                    <!--            data-title="Package {{ e($pkg->package_no) }} — Description"-->
                    <!--            data-bs-toggle="modal"-->
                    <!--            data-bs-target="#descModal"-->
                    <!--            title="Click to view full description">-->
                    <!--      <span class="text-truncate d-inline-block" style="max-width: 320px;">-->
                    <!--        {{ $pkg->description }}-->
                    <!--      </span>-->
                    <!--    </button>-->
                    <!--  @else-->
                    <!--    <span class="text-sm text-muted">—</span>-->
                    <!--  @endif-->
                    <!--</td>-->
                   <td class="pkg-desc">
                      @php $desc = trim((string)($pkg->description ?? '')); @endphp
                      @if($desc !== '')
                        <span class="pkg-desc-text">{{ $desc }}</span>
                      @else
                        <span class="text-sm text-muted">—</span>
                      @endif
                    </td>


                    <td>
                      <span class="badge bg-gradient-info">{{ optional($pkg->method)->name ?? '—' }}</span>
                    </td>
                    <td class="text-center">
                      <span class="text-sm">{{ number_format((float)($pkg->estimated_cost_bdt ?? 0), 2) }}</span>
                    </td>
                    <td class="text-center">
                      <span class="text-sm">{{ optional($pkg->assignedOfficer)->name ?: 'Unassigned' }}</span>
                    </td>
                    <td class="text-center">
                      <span class="text-sm">{{ $pkg->fiscal_year ?? '—' }}</span>
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
                    <td colspan="8" class="text-center py-4 text-sm text-secondary">
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

    </div>
  </div>
</div>

{{-- Hidden form for bulk delete (kept outside the table to avoid nesting forms) --}}
<form id="bulkDeleteForm" action="{{ route('packages.bulkDelete') }}" method="POST" class="d-none">
  @csrf
</form>

<script>
  (function () {
    const selectAll = document.getElementById('selectAllPackages');
    const btn       = document.getElementById('bulkDeleteBtn');
    const countEl   = document.getElementById('bulkDeleteCount');
    const form      = document.getElementById('bulkDeleteForm');

    const boxes = () => Array.from(document.querySelectorAll('.pkg-select'));
    const checked = () => boxes().filter(b => b.checked);

    function refresh() {
      const n = checked().length;
      countEl.textContent = n;
      btn.classList.toggle('d-none', n === 0);
      if (selectAll) {
        const all = boxes();
        selectAll.checked = all.length > 0 && n === all.length;
        selectAll.indeterminate = n > 0 && n < all.length;
      }
    }

    if (selectAll) {
      selectAll.addEventListener('change', function () {
        boxes().forEach(b => { b.checked = selectAll.checked; });
        refresh();
      });
    }

    document.addEventListener('change', function (e) {
      if (e.target.classList && e.target.classList.contains('pkg-select')) refresh();
    });

    btn.addEventListener('click', function () {
      const ids = checked().map(b => b.value);
      if (!ids.length) return;
      if (!confirm('Delete ' + ids.length + ' selected package(s)? This action cannot be undone.')) return;

      form.querySelectorAll('input[name="ids[]"]').forEach(i => i.remove());
      ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
      });
      form.submit();
    });

    refresh();
  })();
</script>

@endsection

<!-- Description Modal -->
<!--<div class="modal fade" id="descModal" tabindex="-1" aria-labelledby="descModalLabel" aria-hidden="true">-->
<!--  <div class="modal-dialog modal-lg modal-dialog-scrollable">-->
<!--    <div class="modal-content">-->
<!--      <div class="modal-header">-->
<!--        <h6 class="modal-title" id="descModalLabel">Description</h6>-->
<!--        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
<!--      </div>-->
<!--      <div class="modal-body">-->
<!--        <pre class="mb-0" id="descModalBody" style="white-space:pre-wrap; font-family: inherit;"></pre>-->
<!--      </div>-->
<!--      <div class="modal-footer">-->
<!--        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->
<!-- <script>
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.desc-trigger');
    if (!btn) return;
    const title = btn.getAttribute('data-title') || 'Description';
    const body  = btn.getAttribute('data-desc') || '—';
    document.getElementById('descModalLabel').textContent = title;
    document.getElementById('descModalBody').textContent  = body;
  });
</script> -->



