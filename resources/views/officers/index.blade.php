@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <div>
        <h6 class="mb-0">Assigned Officer Management</h6>
        <small class="text-muted">Inactive officers are not offered when creating a package or adding a requisition.</small>
      </div>

      <a href="{{ route('officers.create') }}" class="btn btn-primary btn-sm mb-0">
        <i class="fas fa-plus me-1"></i> Add Officer
      </a>
    </div>

    <div class="card-body pt-3">

      @if(session('success'))
        <div class="alert alert-success text-white" role="alert">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger text-white" role="alert">{{ session('error') }}</div>
      @endif

      <div class="table-responsive">
        <table id="officersTable" class="table align-items-center mb-0" style="width:100%">
          <thead class="thead-light">
            <tr>
              <th class="text-start">Officer Name</th>
              <th class="text-center">Status</th>
              <th class="text-center">Packages</th>
              <th class="text-center">Created</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($officers as $officer)
              <tr>
                <td class="text-start">{{ $officer->name }}</td>
                <td class="text-center" data-order="{{ $officer->is_active ? 1 : 0 }}">
                  @if($officer->is_active)
                    <span class="badge bg-gradient-success">Active</span>
                  @else
                    <span class="badge bg-gradient-secondary">Inactive</span>
                  @endif
                </td>
                <td class="text-center">{{ $officer->packages_count }}</td>
                <td class="text-center">{{ optional($officer->created_at)->format('Y-m-d') ?? '—' }}</td>
                <td class="text-end">
                  <a href="{{ route('officers.edit', $officer) }}" class="btn btn-link text-primary px-2 mb-0">
                    <i class="fas fa-edit me-1"></i> Edit
                  </a>
                  <form action="{{ route('officers.destroy', $officer) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Delete this officer? Use Inactive instead if they have any history.');">
                    @csrf @method('DELETE')
                    <button class="btn btn-link text-danger px-2 mb-0" type="submit">
                      <i class="fas fa-trash me-1"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<script>
  $(function () {
    $('#officersTable').DataTable({
      order: [],   // keep the server order (newest created first)
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [
        { targets: 4, orderable: false, className: 'text-end align-middle' },
        { targets: 0, className: 'text-start align-middle' },
        { targets: [1, 2, 3], className: 'text-center align-middle' }
      ],
      language: {
        emptyTable: 'No officers yet.',
        zeroRecords: 'No matching officers.'
      }
    });
  });
</script>
@endsection
