<div>
    <a data-id="formDelete{{ $id }}" type="button" id="btnDelete{{ $id }}"
        class="{{ $class ?? 'btn-delete btn btn-icon btn-active-light-primary w-30px h-30px me-3' }}">
        <i class="ki-duotone btn-delete ki-basket fs-2" data-id="formDelete{{ $id }}">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3" data-id="formDelete{{ $id }}"></span>
        </i>
    </a>
    <form id="formDelete{{ $id }}" action="{{ $action }}" method="post">
        @csrf
        @method('delete')
    </form>
</div>
