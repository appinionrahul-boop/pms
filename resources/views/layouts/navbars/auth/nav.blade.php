<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <h6 class="font-weight-bolder mb-0">@yield('page-title','')</h6>
    </nav>

    <ul class="navbar-nav justify-content-end">
      @php
        $unseenCount = \App\Models\Notificaton::where('is_seen', false)->count();
      @endphp

      <li class="nav-item d-flex align-items-center" style="margin-right:30px;">
        <a href="javascript:void(0)" id="btnNotifications" class="nav-link text-body font-weight-bold px-0">
          <i class="fa fa-bell me-sm-1 position-relative">
            @if($unseenCount > 0)
              <span id="notificationBadge"
                    class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" style="margin-left: 84px;">
                {{ $unseenCount }}
              </span>
            @else
              <span id="notificationBadge" class="d-none"></span>
            @endif
          </i>
          <span class="d-sm-inline d-none">Notificaton</span>
        </a>
      </li>
        <li class="nav-item d-flex align-items-center">
          <a href="{{ route('logout') }}" class="nav-link text-body font-weight-bold px-0">
            <i class="fa fa-user me-sm-1"></i>
            <span class="d-sm-inline d-none">Logout</span>
          </a>
       </li>
    </ul>
  </div>
  <!-- Notifications Modal -->
<div class="modal fade" id="notificationsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title mb-0">Notifications</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="notificationsBody" class="modal-body p-0">
        <div class="p-3 text-muted">Loading...</div>
      </div>
    </div>
  </div>
</div>
</nav>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const btn = document.getElementById('btnNotifications');
  const modalEl = document.getElementById('notificationsModal');
  const bodyEl  = document.getElementById('notificationsBody');
  const badgeEl = document.getElementById('notificationBadge');

  const bsModal = new bootstrap.Modal(modalEl);

  btn.addEventListener('click', async () => {
    // Load the list
    try {
      bodyEl.innerHTML = '<div class="p-3 text-muted">Loading...</div>';
      const res = await fetch('{{ route('notifications.list') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      const html = await res.text();
      bodyEl.innerHTML = html;
    } catch (e) {
      bodyEl.innerHTML = '<div class="p-3 text-danger">Failed to load notifications.</div>';
    }

    // Mark all as seen (and zero-out badge)
    try {
      await fetch('{{ route('notifications.markAllSeen') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({})
      });
      if (badgeEl) {
        badgeEl.textContent = '';
        badgeEl.classList.add('d-none');
      }
    } catch (e) {
      // no-op
    }

    // Show modal
    bsModal.show();
  });
});
</script>

