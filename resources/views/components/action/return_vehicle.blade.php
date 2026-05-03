@if($status == "APPROVED_BY_KADIV_HC")
    <a href="{{$action}}" class="btn btn-sm btn-primary shadow mb-2"></i>Konfirmasi</a>
@else
    <span class="badge bg-success">Di Kembalikan</span>
@endif
