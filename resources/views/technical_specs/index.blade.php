@extends('layouts.user_type.auth')

@section('content') 
<!-- <div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <div>
        <h6 class="mb-0">Technical Specifications</h6>
        <small class="text-muted">Search by Package ID or Package No</small>
      </div>
      <div>
        <a href="{{ route('techspecs.create') }}" class="btn btn-sm bg-gradient-primary">
          <i class="fas fa-plus me-1"></i> Add New
        </a>
      </div>
    </div>

    <div class="card-body pt-3">
      <form method="GET" action="{{ route('techspecs.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
          <input type="text" name="q" class="form-control" value="{{ $q }}" placeholder="Search Package ID / Package No">
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-secondary w-100"><i class="fas fa-search me-1"></i> Search</button>
        </div>
        <div class="col-md-2">
          <a href="{{ route('techspecs.index') }}" class="btn btn-outline-dark w-100">Reset</a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table align-items-center mb-0">
          <thead>
            <tr>
              <th>Package ID</th>
              <th>Package No</th>
              <th>Requisition Created Date</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
          @forelse($packages as $p)
            @php
              $firstReq = $p->requisitions->first();
              $created  = $firstReq?->created_at?->format('Y-m-d') ?? '—';
            @endphp
            <tr>
              <td>{{ $p->package_id }}</td>
              <td>{{ $p->package_no }}</td>
              <td>{{ $created }}</td>
              <td class="text-end">
                <a href="{{ route('techspecs.show', $p) }}" class="btn btn-link text-secondary px-2 mb-0">
                  <i class="fas fa-eye me-1"></i> View
                </a>
                <a href="{{ route('techspecs.createForPackage', $p) }}" class="btn btn-link text-success px-2 mb-0">
                  <i class="fas fa-plus me-1"></i> Add New
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-secondary py-3">No packages found.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
        

      </div>

      <div class="mt-3 px-3">
        {{ $packages->links() }}
      </div>
    </div>
  </div>
</div> -->

<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <div>
        <h6 class="mb-0">Technical Specifications</h6>
        <!-- <small class="text-muted">Search by Package ID or Package No (use the table’s search box)</small> -->
      </div>
      <div>
        <a href="{{ route('techspecs.create') }}" class="btn btn-sm bg-gradient-primary">
          <i class="fas fa-plus me-1"></i> Add New
        </a>
      </div>
    </div>

    <div class="card-body pt-3">
      <div class="table-responsive">
        <table id="packagesTable" class="table table-striped align-items-center mb-0" style="width:100%">
          <thead>
            <tr>
              <th>Package ID</th>
              <th>Package No</th>
              <th>Requisition Created Date</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
          @forelse($packages as $p)
            @php
              $firstReq = $p->requisitions->first();
              $created  = $firstReq?->created_at?->format('Y-m-d') ?? '—';
            @endphp
            <tr>
              <td>{{ $p->package_id }}</td>
              <td>{{ $p->package_no }}</td>
              <td>{{ $created }}</td>
              <td class="text-end">
                <a href="{{ route('techspecs.show', $p) }}" class="btn btn-link text-secondary px-2 mb-0">
                  <i class="fas fa-eye me-1"></i> View
                </a>
                <a href="{{ route('techspecs.createForPackage', $p) }}" class="btn btn-link text-success px-2 mb-0">
                  <i class="fas fa-plus me-1"></i> Add New
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-secondary py-3">No packages found.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script>
  $(function () {
    $('#packagesTable').DataTable({
      order: [[2, 'desc']],     // default sort by date
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [
        { targets: 3, orderable: false }, // disable sort on Action
        { targets: [0,1,2], className: 'align-middle' },
        { targets: 3, className: 'text-end align-middle' }
      ],
      language: {
        search: 'Search:',
        searchPlaceholder: 'Package ID / Package No...'
      }
    });
  });
</script>



@endsection

