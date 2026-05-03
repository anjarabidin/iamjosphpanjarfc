{{-- <form action="{{ $action }}" method="POST" class="d-inline" id="form-send{{ $id }}" enctype="multipart/form-data"
    data-id="form-send{{ $id }}">
    <!-- Fixed the form id to be unique for each form -->
    @csrf
    <input type="hidden" name="id" value="{{ $id }}">
    <button type="submit" class="btn btn-icon btn-send btn-active-light-primary w-30px h-30px me-3">
        <i class="ki-duotone ki-send fs-2">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </button>
</form> --}}

<div>
    <a data-id="formSend{{ $id }}" type="button" id="btnSend{{ $id }}"
        class="btn-send btn btn-icon btn-active-light-primary w-30px h-30px me-3">
        <i class="ki-duotone ki-send fs-2" data-id="formSend{{ $id }}">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3" data-id="formSend{{ $id }}"></span>
        </i>

    </a>
    <form id="formSend{{ $id }}" action="{{ $action }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $id }}">
    </form>
</div>
