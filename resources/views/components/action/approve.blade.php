@if ($data->status == "PENDING")
<div>
    <a data-id="formApprove{{$data->id}}" type="button" id="btnApprove{{$data->id}}"
        class="btn-approve btn btn-icon btn-active-light-primary w-30px h-30px me-3">
        <i class=" fas fa-check" data-id="formApprove{{$data->id}}"></i>
    </a>
    <form id="formApprove{{$data->id}}" action="{{$action}}" method="post">
        @csrf
        @method('PATCH')
        <input type="text" name="status" value="PAID" hidden>
    </form>
</div>
@endif
