<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <h6 class="font-weight-bolder mb-0">@yield('page-title','')</h6>
    </nav>

    <ul class="navbar-nav justify-content-end">
        <li class="nav-item d-flex align-items-center">
          <a href="{{ route('logout') }}" class="nav-link text-body font-weight-bold px-0">
            <i class="fa fa-user me-sm-1"></i>
            <span class="d-sm-inline d-none">Logout</span>
          </a>
       </li>
    </ul>
  </div>
</nav>
