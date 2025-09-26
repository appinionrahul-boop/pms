@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <h6 class="mb-0">
        {{ isset($user) ? 'Edit User' : 'Add User' }}
      </h6>
      <a href="{{ route('users.management') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card-body pt-3">
      <form method="POST"
            action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}">
        @csrf
        @if(isset($user))
          @method('PUT')
        @endif

        <div class="row">
          {{-- Name --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $user->name ?? '') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          {{-- Email --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email', $user->email ?? '') }}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
        </div>

        <div class="row">
          {{-- Phone --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control"
                   value="{{ old('phone', $user->phone ?? '') }}">
            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          {{-- Location --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control"
                   value="{{ old('location', $user->location ?? '') }}">
            @error('location') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
        </div>

        <div class="row">
          {{-- Is Super --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">User Role</label>
            <select name="is_super" class="form-control">
              <option value="0" {{ old('is_super', $user->is_super ?? '0') == '0' ? 'selected' : '' }}>Normal User</option>
              <option value="1" {{ old('is_super', $user->is_super ?? '0') == '1' ? 'selected' : '' }}>Super User</option>
            </select>
            @error('is_super') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          {{-- About Me --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">About Me</label>
            <textarea name="about_me" class="form-control" rows="1">{{ old('about_me', $user->about_me ?? '') }}</textarea>
            @error('about_me') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
        </div>

        {{-- Password fields (only on create) --}}
        @if(!isset($user))
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control" required minlength="6">
              @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
              <input type="password" name="password_confirmation" class="form-control" required minlength="6">
            </div>
          </div>
        @endif

        <div class="text-end">
          <button type="submit" class="btn btn-primary">
            {{ isset($user) ? 'Update User' : 'Create User' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
