@if ($status == true)
    <form action="{{ $action }}" method="POST" class="d-inline" id="status{{ $id }}">
        @csrf
        <input type="hidden" name="id" value="{{ $id }}">
        <a data-id="status{{ $id }}" type="button" class="btn btn-sm btn-danger no-wrap shadow mb-2 btn-status">
            Nonaktifkan
        </a>
    </form>
@else
    <form action="{{ $action }}" method="POST" class="d-inline" id="status{{ $id }}">
        @csrf
        <input type="hidden" name="id" value="{{ $id }}">
        <a data-id="status{{ $id }}" type="button"
            class="btn btn-sm btn-success no-wrap shadow mb-2 btn-status">
            Aktifkan
        </a>
    </form>
@endif
