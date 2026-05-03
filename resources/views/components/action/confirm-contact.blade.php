<div>
    <form action="{{ $action }}" method="post">
        @csrf
        @method('PUT') {{-- atau 'PATCH', tergantung route-mu --}}
        <button type="submit" class="btn btn-sm btn-primary shadow mb-2 d-flex align-items-center justify-content-center"
            title="Konfirmasi data">
            <i style="font-size: 13px;" class="fa fa-check me-1"></i>
            Konfirmasi
        </button>
    </form>
</div>
