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

            <div class="mb-3 position-relative">
              <label>Current Password</label>
              <input type="password" name="current_password" id="current_password" class="form-control pe-5" required>
              <span class="password-toggle" onclick="togglePassword('current_password')">👁</span>
              @error('current_password') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="mb-3 position-relative">
              <label>New Password</label>
              <input type="password" name="new_password" id="new_password" class="form-control pe-5" required>
              <span class="password-toggle" onclick="togglePassword('new_password')">👁</span>
              @error('new_password') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="mb-3 position-relative">
              <label>Confirm New Password</label>
              <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control pe-5" required>
              <span class="password-toggle" onclick="togglePassword('new_password_confirmation')">👁</span>
            </div>

            <button type="submit" class="btn btn-info w-100">Update Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .password-toggle {
    position: absolute;
    top: 71%;
    right: 15px;
    transform: translateY(-50%);
    cursor: pointer;
    user-select: none;
    font-size: 1rem;
    color: #555;
  }
</style>

<script>
  function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    input.type = input.type === "password" ? "text" : "password";
  }
</script>
@endsection
