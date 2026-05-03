<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var toast = Swal.mixin({
        toast: true,
        title: 'General Title',
        animation: false,
        position: 'top-right',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
});
</script>
@if ($message = Session::get('success'))
<script type="text/javascript">
  toast.fire({
      animation: true,
      title: 'Sukses',
      text:'{{$message}}',
      icon:'success'
  });
</script>
@endif
@if ($message = Session::get('error'))
<script type="text/javascript">
Swal.fire(
  'Error',
  '{{$message}}',
  'error'
)
</script>
@endif
@if ($message = Session::get('warning'))
<script type="text/javascript">
  toast.fire({
      animation: true,
      title: 'Warning',
      text:'{{$message}}',
      icon:'warning'
  });
</script>
@endif

