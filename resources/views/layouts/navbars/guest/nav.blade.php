<!-- Navbar -->
<nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 my-3 
  {{ (Request::is('static-sign-up') ? 'w-100 shadow-none navbar-transparent mt-4' : 'blur blur-rounded shadow py-2 start-0 end-0 mx-4') }}">
  <div class="container-fluid {{ (Request::is('static-sign-up') ? 'container' : 'container-fluid') }} d-flex align-items-center">

    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center" href="{{ url('dashboard') }}">
      <img src="{{ asset('assets/img/ompany-logo.jpg') }}" alt="Company Logo" style="height:40px;">
    </a>

    <!-- Centered Company Name -->
    <div class="mx-auto text-center">
      <span class="navbar-text font-weight-bolder {{ (Request::is('static-sign-up') ? 'text-white' : '') }}">
        Bangladesh-China Power Company Limited
      </span>
    </div>

    <!-- Mobile toggle button -->
    <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" 
      data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon mt-2">
        <span class="navbar-toggler-bar bar1"></span>
        <span class="navbar-toggler-bar bar2"></span>
        <span class="navbar-toggler-bar bar3"></span>
      </span>
    </button>

  </div>
</nav>
<!-- End Navbar -->
