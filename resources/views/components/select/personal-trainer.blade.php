@if (@$attributes['multiple'])
<select name="personal_trainer_id[]" id="personal_trainer_id" data-control="select2" class="form-control {{$class ?? ''}}" {{ $attributes }}>
    @foreach (@$value ?? [] as $personalTrainer )
    <option selected value="{{$personalTrainer->id}}">{{$personalTrainer->name}}</option>
    @endforeach
</select>
@else
<select name="personal_trainer_id" id="personal_trainer_id" data-control="select2" class="form-control {{$class ?? ''}}" {{ $attributes }}>
    @if (@$value)
    <option selected value="{{$value->id}}">{{$value->name}}</option>
    @endif
</select>
@endif

@push('js')
<script>
    $(document).ready(function() {
        $('#personal_trainer_id').select2({
            placeholder: "Pilih Personal Trainer",
            ajax: {
                url: "{{route('select2')}}",
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    var queryParameters = {
                        search: params.term,
                        data_type: "PERSONAL_TRAINER",
                    }
                    return queryParameters;
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name.toUpperCase(),
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });
    });
</script>
@endpush
