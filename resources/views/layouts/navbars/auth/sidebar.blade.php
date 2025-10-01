<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 overflow-y: auto;" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
      {{-- Use asset() so the logo always resolves from /public --}}
      <img src="{{ asset('assets/img/logo-ct.png') }}" class="navbar-brand-img h-100" alt="Logo">
      <span class="ms-3 font-weight-bold">Procurement Management System</span>
    </a>
  </div>

  <hr class="horizontal dark mt-0">

  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      {{-- Dashboard --}}
      <li class="nav-item">
        <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            {{-- Inline SVG (shop) --}}
            <svg width="12px" height="12px" viewBox="0 0 45 40" xmlns="http://www.w3.org/2000/svg">
              <g fill="none" fill-rule="evenodd">
                <g fill="#FFFFFF" fill-rule="nonzero">
                  <g>
                    <g>
                      <path class="color-background opacity-6" d="M46.72,10.74 L40.845,0.95 C40.491,0.361 39.854,0 39.167,0 L7.833,0 C7.146,0 6.509,0.361 6.155,0.95 L0.28,10.741 C0.097,11.046 0,11.395 0,11.75 C-0.008,16.066 3.484,19.573 7.8,19.583 L7.816,19.583 C9.75,19.588 11.617,18.873 13.052,17.576 C16.017,20.256 20.529,20.256 23.494,17.576 C26.46,20.262 30.979,20.262 33.946,17.576 C36.242,19.648 39.544,20.171 42.368,18.91 C45.193,17.65 47.008,14.843 47,11.75 C47,11.395 46.903,11.046 46.72,10.741 Z"/>
                      <path class="color-background" d="M39.198,22.491 C37.378,22.493 35.582,22.015 33.952,21.095 L33.922,21.111 C31.143,22.684 27.926,22.932 24.984,21.8 C24.475,21.605 23.978,21.372 23.496,21.102 L23.475,21.113 C20.696,22.687 17.479,22.934 14.539,21.8 C14.03,21.605 13.533,21.372 13.052,21.102 C11.425,22.019 9.632,22.495 7.816,22.491 C7.165,22.484 6.516,22.417 5.875,22.291 L5.875,44.722 C5.875,45.95 6.752,46.945 7.833,46.945 L19.583,46.945 L19.583,33.607 L27.417,33.607 L27.417,46.945 L39.167,46.945 C40.248,46.945 41.125,45.95 41.125,44.722 L41.125,22.282 C40.489,22.412 39.844,22.482 39.198,22.491 Z"/>
                    </g>
                  </g>
                </g>
              </g>
            </svg>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      {{-- Package Management --}}
      <li class="nav-item mt-2">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Package Management</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::is('packages*') ? 'active' : '' }}" href="{{ route('packages.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            {{-- Original package inline SVG --}}
            <svg width="12px" height="12px" viewBox="0 0 46 42" xmlns="http://www.w3.org/2000/svg">
              <g fill="none" fill-rule="evenodd">
                <g fill="#FFFFFF" fill-rule="nonzero">
                  <g>
                    <g>
                      <path class="color-background" d="M45,0 L26,0 C25.447,0 25,0.447 25,1 L25,20 C25,20.379 25.214,20.725 25.553,20.895 C25.694,20.965 25.848,21 26,21 C26.212,21 26.424,20.933 26.6,20.8 L34.333,15 L45,15 C45.553,15 46,14.553 46,14 L46,1 C46,0.447 45.553,0 45,0 Z" opacity="0.6"/>
                      <path class="color-foreground" d="M22.883,32.86 C20.761,32.012 17.324,31 13,31 C8.676,31 5.239,32.012 3.116,32.86 C1.224,33.619 0,35.438 0,37.494 L0,41 C0,41.553 0.447,42 1,42 L25,42 C25.553,42 26,41.553 26,41 L26,37.494 C26,35.438 24.776,33.619 22.883,32.86 Z"/>
                      <path class="color-foreground" d="M13,28 C17.432,28 21,22.529 21,18 C21,13.589 17.411,10 13,10 C8.589,10 5,13.589 5,18 C5,22.529 8.568,28 13,28 Z"/>
                    </g>
                  </g>
                </g>
              </g>
            </svg>
          </div>
          <span class="nav-link-text ms-1">App Management</span>
        </a>
      </li>

      {{-- Requisition Management --}}
      <!-- <li class="nav-item mt-2">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Requisition Management</h6>
      </li> -->
      <li class="nav-item pb-2">
        <a class="nav-link {{ Request::is('requisitions*') ? 'active' : '' }}" href="{{ route('requisitions.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            {{-- Inline SVG (document) --}}
            <svg width="12px" height="12px" viewBox="0 0 40 44" xmlns="http://www.w3.org/2000/svg">
              <g fill="none" fill-rule="evenodd">
                <g fill="#FFFFFF" fill-rule="nonzero">
                  <g>
                    <g>
                      <path class="color-background" d="M40,40 L36.364,40 L36.364,3.636 L5.455,3.636 L5.455,0 L38.182,0 C39.185,0 40,0.815 40,1.818 L40,40 Z" opacity="0.6"/>
                      <path class="color-background" d="M30.909,7.273 L1.818,7.273 C0.815,7.273 0,8.087 0,9.091 L0,41.818 C0,42.822 0.815,43.636 1.818,43.636 L30.909,43.636 C31.913,43.636 32.727,42.822 32.727,41.818 L32.727,9.091 C32.727,8.087 31.913,7.273 30.909,7.273 Z M18.182,34.545 L7.273,34.545 L7.273,30.909 L18.182,30.909 L18.182,34.545 Z M25.455,27.273 L7.273,27.273 L7.273,23.636 L25.455,23.636 L25.455,27.273 Z M25.455,20 L7.273,20 L7.273,16.364 L25.455,16.364 L25.455,20 Z"/>
                    </g>
                  </g>
                </g>
              </g>
            </svg>
          </div>
          <span class="nav-link-text ms-1">Requisition Management</span>
        </a>
      </li>

      {{-- Technical Specification --}}
      <!-- <li class="nav-item mt-2">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Technical Specification</h6>
      </li> -->
      <li class="nav-item pb-2">
        <a class="nav-link {{ Request::is('technical-specs*') ? 'active' : '' }}" href="{{ route('techspecs.index') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            {{-- Inline SVG (settings/cogs style) --}}
            <svg width="12px" height="12px" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
              <g fill="none" fill-rule="evenodd">
                <g fill="#FFFFFF" fill-rule="nonzero">
                  <g>
                    <g>
                      <polygon class="color-background" opacity="0.6" points="18.088 15.732 11.178 8.822 13.333 6.667 6.667 0 0 6.667 6.667 13.333 8.822 11.178 15.315 17.672"></polygon>
                      <path class="color-background" opacity="0.6" d="M31.567,23.233 C31.052,23.293 30.53,23.333 30,23.333 C29.492,23.333 28.987,23.303 28.48,23.245 L22.412,30.743 L29.942,38.273 C32.243,40.575 35.973,40.575 38.275,38.273 C40.577,35.972 40.577,32.242 38.275,29.94 L31.567,23.233 Z"></path>
                      <path class="color-background" d="M33.785,11.285 L28.715,6.215 L34.062,0.868 C32.82,0.315 31.448,0 30,0 C24.477,0 20,4.477 20,10 C20,10.99 20.148,11.943 20.417,12.847 L2.435,27.397 C0.95,28.708 0.063,30.595 0.003,32.573 C-0.058,34.553 0.71,36.492 2.11,37.89 C3.47,39.252 5.278,40 7.202,40 C9.267,40 11.237,39.113 12.603,37.565 L27.153,19.583 C28.057,19.852 29.01,20 30,20 C35.523,20 40,15.523 40,10 C40,8.552 39.685,7.18 39.132,5.937 L33.785,11.285 Z"></path>
                    </g>
                  </g>
                </g>
              </g>
            </svg>
          </div>
          <span class="nav-link-text ms-1">Technical Specification</span>
        </a>
      </li>
      <li class="nav-item mt-2">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Reports</h6>
      </li>
      {{-- Reports --}}
      <li class="nav-item">
        <a class="nav-link {{ Request::is('reports/procurements') ? 'active' : '' }}" href="{{ route('reports.procurements') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            {{-- Inline SVG (office/table) --}}
            <svg width="12px" height="12px" viewBox="0 0 42 42" xmlns="http://www.w3.org/2000/svg">
              <g fill="none" fill-rule="evenodd">
                <g fill="#FFFFFF" fill-rule="nonzero">
                  <g>
                    <g>
                      <path class="color-background opacity-6" d="M12.25,17.5 L8.75,17.5 L8.75,1.75 C8.75,0.782 9.532,0 10.5,0 L31.5,0 C32.468,0 33.25,0.782 33.25,1.75 L33.25,12.25 L29.75,12.25 L29.75,3.5 L12.25,3.5 L12.25,17.5 Z"></path>
                      <path class="color-background" d="M40.25,14 L24.5,14 C23.532,14 22.75,14.782 22.75,15.75 L22.75,38.5 L19.25,38.5 L19.25,22.75 C19.25,21.782 18.468,21 17.5,21 L1.75,21 C0.782,21 0,21.782 0,22.75 L0,40.25 C0,41.218 0.782,42 1.75,42 L40.25,42 C41.218,42 42,41.218 42,40.25 L42,15.75 C42,14.782 41.218,14 40.25,14 Z M12.25,36.75 L7,36.75 L7,33.25 L12.25,33.25 L12.25,36.75 Z M12.25,29.75 L7,29.75 L7,26.25 L12.25,26.25 L12.25,29.75 Z M35,36.75 L29.75,36.75 L29.75,33.25 L35,33.25 L35,36.75 Z M35,29.75 L29.75,29.75 L29.75,26.25 L35,26.25 L35,29.75 Z M35,22.75 L29.75,22.75 L29.75,19.25 L35,19.25 L35,22.75 Z"></path>
                    </g>
                  </g>
                </g>
              </g>
            </svg>
          </div>
          <span class="nav-link-text ms-1">Reports</span>
        </a>
      </li>
      <!-- Settings -->
       <li class="nav-item mt-2">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Settings</h6>
      </li>
      <li class="nav-item">
  <a class="nav-link {{ Request::is('change-password') ? 'active' : '' }}" href="{{ route('change.password') }}">
    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
      {{-- Lock Icon --}}
      <svg width="12px" height="12px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <g fill="none" fill-rule="evenodd">
          <g fill="#FFFFFF" fill-rule="nonzero">
            <path class="color-background opacity-6" d="M12 2a5 5 0 00-5 5v3H6c-1.1 0-2 .9-2 2v8c0 
              1.1.9 2 2 2h12c1.1 0 2-.9 
              2-2v-8c0-1.1-.9-2-2-2h-1V7a5 
              5 0 00-5-5zm-3 8V7a3 3 0 
              016 0v3H9z"/>
          </g>
        </g>
      </svg>
    </div>
    <span class="nav-link-text ms-1">Change Password</span>
  </a>
</li>
@auth
  @if(Auth::user()->is_super == 1)
    <li class="nav-item mt-2">
      <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Administration</h6>
    </li>
    {{-- User Management --}}
    <li class="nav-item">
      <a class="nav-link {{ Request::is('users/management') ? 'active' : '' }}" href="{{ route('users.management') }}">
        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
          {{-- Inline SVG (users icon) --}}
          <svg width="12px" height="12px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <g fill="none" fill-rule="evenodd">
              <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 
              1.79-4 4 1.79 4 4 4zm0 2c-2.67 
              0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" 
              fill="#FFFFFF" class="color-background"></path>
            </g>
          </svg>
        </div>
        <span class="nav-link-text ms-1">User Management</span>
      </a>
    </li>
  @endif
@endauth
    </ul>
  </div>
</aside>

<style>
  .g-sidenav-show .sidenav {
  position: fixed !important;
  top: 0; left: 0;
  height: 100vh !important;
  max-height: none !important;
  overflow-y: hidden !important;
  overflow-x: hidden !important;
}


.g-sidenav-show .sidenav .navbar-collapse {
  overflow: visible !important;
}


.g-sidenav-show .main-content {
  margin-left: 250px;              
  min-height: 100vh;
  overflow: auto;                  
}
</style>
