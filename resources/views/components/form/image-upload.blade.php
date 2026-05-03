@push('css')
<style type="text/css">
    /* image preview */
    .img-upload .imagepreview {
        width: 100%;
        height: 183px;
        border: 1px dashed #000;
        color: #ecf0f1;
        position: relative;
        background-size: auto 183px !important;
        object-fit: contain;
        background-repeat: no-repeat !important;
        background-position: center !important;
    }

    .img-upload .text {
        font-size: 14px;
        margin-top: 15px;
    }

    .img-upload .imagepreview input {
        width: 120px;
        height: 40px;
        z-index: 10;
        position: absolute;
        top: 60%;
        left: 50%;
        transform: translateX(-50%);
        margin-left: 0px;
        cursor: pointer;
        opacity: 0;
    }

    .img-upload .imagepreview input:focus {
        outline: 0px;
    }

    .img-upload .imagepreview label {
        z-index: 5;
        width: 120px;
        height: 40px;
        background-color: #ffffff;
        background-size: cover;
        color: #143250;
        font-size: 14px;
        line-height: 40px;
        top: 60%;
        left: 50%;
        transform: translateX(-50%);
        right: 0;
        margin-left: 0px;
        bottom: 0px;
        margin-bottom: 0px;
        text-align: center;
        position: absolute;
        cursor: pointer;
        -webkit-transition: all 0.3s ease-in;
        -o-transition: all 0.3s ease-in;
        transition: all 0.3s ease-in;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .img-upload .imagepreview label i {
        margin-right: 7px;
    }

    .img-upload .imagepreview label:hover {
        background: #2d3274;
        color: #fff;
    }

    .img-upload .imagepreview label:focus {
        outline: 0px;
    }

    @media screen and (max-width: 403px) {
        .img-upload .imagepreview {
            width: 100% !important;
        }
    }

    /* end image preview */
</style>
@endpush
<div class="row">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <label class="fs-6 fw-bold form-label my-2">{{ $label ?? 'Image' }} <i
                class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                title="Upload foto atau gambar"></i></label>
        @if(@$isDelete)
        {{-- <button data-delete="{{$id}}" class="delete-img btn btn-danger btn-sm fs-8">Hapus</button> --}}
        <a href="{{ $deleteUrl ?? '#' }}" class="">
            <i class="fa-solid fa-trash delete-img cursor-pointer fs-4" data-delete="{{ $id }}"></i>
        </a>
        @endif
    </div>
    <div class="col-lg-12">
        <div class="img-upload custom-image-upload">
            <div id="imagepreview{{ $id ?? '' }}" class="img-preview imagepreview"
                style="background: url('{{ asset(@$value) }}');">
                <label for="imageupload{{ $id ?? '' }}" class="img-label" id="image-label"><i
                        class="fa fa-upload"></i>UPLOAD</label>
                <input accept="image/*" type='file' name="{{ $name ?? 'image' }}" class="img-upload imgUpload"
                    id="imageupload{{ $id ?? '' }}">
            </div>
            <p class="text">Upload gambar dengan tipe .png/.jpg/.jpeg/.gif</p>
        </div>
    </div>
</div>
@push('js')
<script>
    if(!window.location.href.includes('shop-product')) {
            $("#imageupload{{ $id ?? '' }}").on('change', function() {
                if (typeof(FileReader) != "undefined") {
                    var image_holder = $("#imagepreview{{ $id ?? '' }}");
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        console.log(e.target.result);
                        image_holder.attr('style', 'background:url(' + e.target.result + ')')
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                } else {
                    console.log("This browser does not support FileReader.");
                }
            });
        }
</script>
@endpush
