<form action="{{ $action }}" method="POST" class="d-inline" id="is_block{{ $id }}">
    @csrf
    <input type="hidden" name="id" value="{{ $id }}">

    @if ($is_block == true)
    <a data-id="is_block{{ $id }}" type="button" class="btn btn-sm w-110px btn-danger no-wrap btn-block">
        Buka Blokir
    </a>
    @else
    <a data-id="is_block{{ $id }}" type="button" class="btn btn-sm w-110px btn-danger no-wrap btn-block">
        Blokir
    </a>
    @endif
</form>
