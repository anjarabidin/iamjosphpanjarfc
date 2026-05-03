<div>
    <a data-id="formCheckout{{ $id }}" type="button" id="btnCheckout{{ $id }}"
        class="btn-checkout btn btn-icon btn-active-light-primary w-30px h-30px me-3">
        {{-- <i class="ki-duotone btn-delete ki-basket fs-2" data-id="formCheckout{{ $id }}">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3" data-id="formCheckout{{ $id }}"></span>
        </i> --}}
        <i data-id="formCheckout{{ $id }}" class="btn-checkout fas fa-check"></i>
    </a>
    <form id="formCheckout{{ $id }}" action="{{ $action }}" method="post">
        @csrf
        <input type="hidden" name="user_id" value="{{ $id }}">
    </form>
</div>
