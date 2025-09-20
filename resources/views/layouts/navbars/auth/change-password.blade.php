@extends('layouts.user_type.auth')

@section('content')
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-info text-white">
          <h5 class="mb-0">Change Password</h5>
        </div>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          <form method="POST" action="{{ route('change.password.update') }}">
            @csrf

            <div class="mb-3">
              <label>Current Password</label>
              <input type="password" name="current_password" class="form-control" required>
              @error('current_password') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="mb-3">
              <label>New Password</label>
              <input type="password" name="new_password" class="form-control" required>
              @error('new_password') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="mb-3">
              <label>Confirm New Password</label>
              <input type="password" name="new_password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-info w-100">Update Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
