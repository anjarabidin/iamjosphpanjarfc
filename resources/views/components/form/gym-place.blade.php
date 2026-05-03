@php
$gymPlace = \App\Models\GymPlace::find(@$value ?? null);
@endphp
<select name="gym_place_id" id="gym_place_id" class="form-control {{ $class ?? '' }}" {{ $attributes }}>
    @if ($gymPlace)
    <option selected value="{{ @$gymPlace->id }}">{{ @$gymPlace->name }}</option>
    @endif
</select>

@push('js')
<script>
    $(document).ready(function() {
            $('#gym_place_id').select2({
                placeholder: "Pilih Tempat Gym",
                ajax: {
                    url: "{{ route('select2') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        var queryParameters = {
                            search: params.term,
                            data_type: "GYM_PLACE",
                        }
                        return queryParameters;
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.name,
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
