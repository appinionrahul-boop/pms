@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header pb-0 d-flex align-items-center justify-content-between">
          <h6 class="mb-0">Add Officer</h6>
          <a href="{{ route('officers.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
            <i class="fas fa-arrow-left me-1"></i> Back to Officers
          </a>
        </div>

        <div class="card-body">
          <form action="{{ route('officers.store') }}" method="POST" autocomplete="off">
            @csrf
            @include('officers._form', ['submitLabel' => 'Create Officer'])
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
