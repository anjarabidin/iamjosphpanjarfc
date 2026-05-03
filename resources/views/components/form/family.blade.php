@php
if(@$value) {
    $family = \App\Models\UserFamily::find(@$value ?? '');
}
@endphp
<div class="form-group mt-2" id="user_family_id">
    <label for="user_family_id" class="form-label">Keluarga</label>
    <select name="user_family_id" id="user_family_id" {{ $attributes->merge(['class' => 'form-control']) }}>
        <option value="">Pilih Keluarga</option>
        @if(@$family)
            <option selected value="{{@$family->id}}">{{@$family->name}} - {{@$family->relationship}}</option>
        @endif
    </select>
</div>

@push('js')
<script>
    $(document).ready(function() {
        if ('{{@$family}}') {
            $('#user_family_id').show();
        } else {
            $('#user_family_id').hide();
        }
        $('#used_for').on('change', function() {
            if (this.value == 'FAMILY') {
                $('#user_family_id').show();
                var user_id = $('#user_id').val();
        $.ajax({
            url: "{{route('select2.fetch')}}",
            type: "POST",
            data: {
                user_id: user_id,
                _token: "{{csrf_token()}}"
            },
            dataType: "json",
            success: function(data) {
                $('select[name="user_family_id"]').empty();
                $('select[name="user_family_id"]').append('<option value="">Pilih Keluarga</option>');
                $.each(data, function(key, value) {
                    $('select[name="user_family_id"]').append('<option value="' + value.id + '">' + value.name +' - '+ value.relationship +'</option>');
                });
            }
        });
            } else {
                $('#user_family_id').hide();
            }
        });
    });
    $('#user_id').on('change', function() {
        var user_id = $(this).val();
        $.ajax({
            url: "{{route('select2.fetch')}}",
            type: "POST",
            data: {
                user_id: user_id,
                _token: "{{csrf_token()}}"
            },
            dataType: "json",
            success: function(data) {
                $('select[name="user_family_id"]').empty();
                $('select[name="user_family_id"]').append('<option value="">Pilih Keluarga</option>');
                $.each(data, function(key, value) {
                    $('select[name="user_family_id"]').append('<option value="' + value.id + '">' + value.name +' - '+ value.relationship +'</option>');
                });
            }
        });
    });
</script>
@endpush
