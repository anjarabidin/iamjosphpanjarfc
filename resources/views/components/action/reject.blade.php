@if ($status == 'PENDING')
<a data-id="form{{$id}}" type="button"  class="btn btn-sm btn-danger shadow mb-2 btn-reject">Tolak</a>
<form  id="form{{$id}}" action="{{$action}}" method="post">
@csrf
@method('delete')
</form>
@elseif ($status == 'APPROVED')
    <span class="badge bg-success">DITERIMA</span>
@elseif ($status == 'REJECTED')
    <span class="badge bg-danger">DITOLAK</span>
@endif
