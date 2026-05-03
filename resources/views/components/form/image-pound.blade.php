@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />

<link rel='stylesheet'
    href='https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'>
<link rel='stylesheet' href='https://unpkg.com/filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css'>
<style>
    .filepond--drop-label {
        color: #4c4e53;
    }
    .filepond--label-action {
        text-decoration-color: #babdc0;
    }
    .filepond--panel-root {
        border-radius: 2em;
        background-color: #edf0f4;
        height: 1em;
    }
    .filepond--item-panel {
        background-color: #595e68;
    }
    .filepond--drip-blob {
        background-color: #7f8a9a;
    }
    .filepond--item {
        width: calc(20% - 0.5em);
    }
</style>
@endpush

<input type="file" name="file" id="file_pound" multiple  class='p-5' multiple data-allow-reorder="true" data-max-file-size="10MB" data-max-files="{{$amount ?? 10 }}" />
<input type="text" id="file" name="{{$name ?? 'file_pound'}}" hidden>
@push('js')
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-poster/dist/filepond-plugin-file-poster.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src='https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.min.js'></script>
<script src='https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js'>
</script>
<script
    src='https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js'>
</script>

<script>
    FilePond.registerPlugin(
        // add plugin validate type
        FilePondFileValidateTypePlugin,
        // encodes the file as base64 data
        FilePondPluginFileEncode,
        // validates the size of the file
        FilePondPluginFileValidateSize,
        // corrects mobile image orientation
        FilePondPluginImageExifOrientation,
        FilePondPluginFilePoster,
        // previews dropped images
        FilePondPluginImagePreview
    )
        //configuration filepond
        const inputElement = document.querySelector('input[id="file_pound"]');
        // Create a FilePond instance
        const pond = FilePond.create(inputElement);
    //tujuan filepond
    FilePond.setOptions({
        allowMultiple: true,
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/gif'],
        server: {
            process: {
                url: "{{ route('file_pound.store') }}",
                method: 'POST',
                onload: (response) => {
                   var input = $("#file")
                   if(input.val() != ''){
                    $("#file").val(input.val()+','+response)
                   }else{
                    $("#file").val(response)
                   }
                   return response;
                }
            },
            revert: (uniqueFileId, load, error) => {
                deleteImage(uniqueFileId);
                error('Error terjadi saat delete file');
                load();
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
        }
    });
    //end config filepond
    function deleteImage(nameFile){
        $.ajax({
            url: "{{route('file_pound.destroy',1)}}",
            data : {
                file:nameFile
            },
            method: "DELETE",
            type: "JSON",
            success: function(response) {
                console.log(response);
            },
            error: function(response) {
                console.log('error')
            }
        });
    }
</script>

@endpush

