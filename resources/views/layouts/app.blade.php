<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>

@if (\Request::is('rtl'))
  <html dir="rtl" lang="ar">
@else
  <html lang="en" >
@endif

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  @if (env('IS_DEMO'))
      <x-demo-metas></x-demo-metas>
  @endif

  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Procurement Management System
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.3" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('assets/css/soft-ui-dashboard.css') }}">
  <!-- <img src="{{ asset('assets/img/bruce-mars.jpg') }}" alt=""> -->
   <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Responsive: collapse extra columns behind a +/- toggle instead of horizontal scroll -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
  $.extend(true, $.fn.dataTable.defaults, { responsive: true, autoWidth: false });
  // Responsive can't collapse columns while a .table-responsive wrapper still allows
  // horizontal scrolling, so drop the wrapper before each table initialises.
  $(document).on('preInit.dt', function (e, settings) {
    $(settings.nTable).css('width', '100%').closest('.table-responsive').removeClass('table-responsive');
  });
</script>
<style>
  /* keep expanded child-row details readable */
  table.dataTable > tbody > tr.child ul.dtr-details { width: 100%; }
  table.dataTable > tbody > tr.child span.dtr-title { min-width: 160px; font-weight: 600; }

  /* Classic green "+" / red "−" circle instead of the default chevron */
  table.dataTable > tbody > tr > td.dtr-control::before,
  table.dataTable > tbody > tr > th.dtr-control::before {
    content: "+" !important;
    display: inline-block !important;
    box-sizing: content-box;
    width: 15px !important;
    height: 15px !important;
    line-height: 15px !important;
    margin-right: 8px;
    border: 2px solid #fff !important;
    border-radius: 50% !important;
    background-color: #31b131 !important;
    box-shadow: 0 0 3px #444;
    color: #fff !important;
    font-family: 'Courier New', Courier, monospace;
    font-size: 14px;
    font-weight: 700;
    text-align: center;
    text-indent: 0 !important;
    vertical-align: middle;
    cursor: pointer;
  }
  table.dataTable > tbody > tr.dt-hasChild > td.dtr-control::before,
  table.dataTable > tbody > tr.dt-hasChild > th.dtr-control::before {
    content: "-" !important;
    background-color: #d33333 !important;
  }
</style>

</head>

<body class="g-sidenav-show  bg-gray-100 {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }} ">
  <!-- <div class="page-header" style="background-image:url('{{ asset('assets/img/curved-images/curved0.jpg') }}');"> -->
  
@auth
    @yield('auth')
  @endauth
  @guest
    @yield('guest')
  @endguest

  @if(session()->has('success'))
    <div x-data="{ show: true}"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        class="position-fixed bg-success rounded right-3 text-sm py-2 px-4">
      <p class="m-0">{{ session('success')}}</p>
    </div>
  @endif
    <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/fullcalendar.min.js"></script>
  <script src="../assets/js/plugins/chartjs.min.js"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  @stack('rtl')
  @stack('dashboard')
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/soft-ui-dashboard.min.js?v=1.0.3"></script>
</body>

</html>
