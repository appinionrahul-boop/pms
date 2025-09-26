<ul class="list-group list-group-flush">
  @forelse($items as $n)
    <li class="list-group-item d-flex justify-content-between align-items-start">
      <div class="me-3">
        <div class="fw-normal">{{ $n->text }}</div>
        <div class="small text-muted">{{ optional($n->created_at)->format('Y-m-d H:i') }}</div>
      </div>
      @if(!$n->is_seen)
        <span class="badge bg-primary rounded-pill">new</span>
      @endif
    </li>
  @empty
    <li class="list-group-item text-muted">No notifications.</li>
  @endforelse
</ul>
