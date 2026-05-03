
@if ($message = Session::get('success'))
<script type="text/javascript">
  toastr.options =
  {
  	"closeButton" : true,
  	"progressBar" : true
  }
toastr.success("{{ $message }}");
</script>
@endif
@if ($message = Session::get('error'))
<script type="text/javascript">
  toastr.options =
  {
  	"closeButton" : true,
  	"progressBar" : true
  }
toastr.error("{{ $message }}");
</script>
@endif
@if ($message = Session::get('warning'))
<script type="text/javascript">
  toastr.options =
  {
  	"closeButton" : true,
  	"progressBar" : true
  }
toastr.warning("{{ $message }}");
</script>
@endif
