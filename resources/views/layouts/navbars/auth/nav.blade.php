<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <h6 class="font-weight-bolder mb-0">@yield('page-title','')</h6>
    </nav>

    <ul class="navbar-nav justify-content-end">
      @php
        $unseenCount = \App\Models\Notificaton::where('is_seen', false)->count();
      @endphp

        <li class="nav-item d-flex align-items-center me-3">
            <a href="javascript:void(0)" id="btnNotifications"
              class="nav-link p-0 d-flex align-items-center" role="button" aria-label="Notifications">
              <span class="bell-wrap position-relative d-inline-flex align-items-center justify-content-center rounded-circle @if(($unseenCount ?? 0) > 0) has-unseen @endif">
                {{-- Inline SVG bell --}}
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 2a6 6 0 00-6 6v2.09c0 .51-.2 1-.56 1.36L4 13.29V15h16v-1.71l-1.44-1.84a2 2 0 01-.56-1.36V8a6 6 0 00-6-6zm0 20a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>

                @php $badgeText = ($unseenCount ?? 0) > 99 ? '99+' : ($unseenCount ?? 0); @endphp
                @if(($unseenCount ?? 0) > 0)
                  <span id="notificationBadge" class="badge bg-danger text-white position-absolute badge-counter">
                    {{ $badgeText }}
                  </span>
                @else
                  <span id="notificationBadge" class="d-none"></span>
                @endif
              </span>
              <!-- <span class="d-sm-inline d-none ms-2">Notification</span> -->
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
  <style>
    .bell-wrap{ width:38px; height:38px; background:rgba(0,0,0,.06); }
      .badge-counter{ top:-4px; right:-4px; min-width:18px; height:18px; line-height:18px; font-size:10px; padding:0 4px; border-radius:999px; }
      .bell-wrap.has-unseen svg{ animation:bell-ring 1s ease-in-out 2; transform-origin: top center; }
      @keyframes bell-ring{
        0%{transform:rotate(0)}20%{transform:rotate(-15deg)}40%{transform:rotate(12deg)}
        60%{transform:rotate(-8deg)}80%{transform:rotate(4deg)}100%{transform:rotate(0)}
      }
  </style>

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

