@if ($status == 'PENDING')
<a href="{{$create}}" class="btn btn-alt-primary btn-sm shadow my-1" type="button"><i class="fa fa-search opacity-100 me-1"></i> Pilih Teknisi</a><br>
@else
<a href="{{$show}}" class="btn btn-sm btn-success shadow my-1" type="button"><i class="fa fa-eye opacity-100 me-1"></i> Lihat Teknisi</a><br>
@endif
