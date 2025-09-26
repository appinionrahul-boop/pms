@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <h6 class="mb-0">User Management</h6>

      <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        + Add User
      </a>
    </div>

    <div class="card-body pt-3">
      <div class="table-responsive">
        <table class="table align-items-center mb-0" id="usersTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Location</th>
              <th>Is Super</th>
              <th>About Me</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ $user->location }}</td>
                <td>
                  @if($user->is_super == 1)
                    <span class="badge bg-success">Super</span>
                  @else
                    <span class="badge bg-secondary">Normal</span>
                  @endif
                </td>
                <td>{{ $user->about_me }}</td>
                <td class="text-end">
                  <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                      Edit
                    </a>

                    <button
                      class="btn btn-sm btn-outline-dark"
                      data-bs-toggle="modal"
                      data-bs-target="#passwordResetModal"
                      data-user-id="{{ $user->id }}"
                      data-user-name="{{ $user->name }}"
                    >
                      Reset Password
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Password Reset Modal (single reusable) --}}
<div class="modal fade" id="passwordResetModal" tabindex="-1" aria-labelledby="passwordResetModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="passwordResetForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="passwordResetModalLabel">Reset Password</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <p class="small text-muted mb-2">User: <span id="pr-user-name" class="fw-semibold"></span></p>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required minlength="6">
          </div>
          <div class="mb-2">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="6">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark">Update Password</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- DataTable + modal wiring --}}
<script>
  $(function () {
    $('#usersTable').DataTable({
      order: [[0, 'asc']],
      pageLength: 10,
      language: {
        search: 'Search:',
        searchPlaceholder: 'Type to filter...'
      }
    });
  });

  const prModal = document.getElementById('passwordResetModal');
  prModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const userId = button.getAttribute('data-user-id');
    const userName = button.getAttribute('data-user-name');

    // Update modal user name
    document.getElementById('pr-user-name').textContent = userName;

    // Point the form action to the correct route
    const form = document.getElementById('passwordResetForm');
    form.action = "{{ url('/users') }}/" + userId + "/password";
  });
</script>
@endsection
