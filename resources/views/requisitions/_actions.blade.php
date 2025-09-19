<a href="{{ $view }}" class="btn btn-link text-secondary px-2 mb-0">
  <i class="fas fa-eye me-1"></i> View
</a>
<a href="{{ $edit }}" class="btn btn-link text-primary px-2 mb-0">
  <i class="fas fa-edit me-1"></i> Edit
</a>
<form action="{{ $del }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this requisition?');">
  @csrf @method('DELETE')
  <button class="btn btn-link text-danger px-2 mb-0" type="submit">
    <i class="fas fa-trash me-1"></i> Delete
  </button>
</form>