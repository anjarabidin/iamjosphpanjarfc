<div class="modal fade" id="modal_install_app" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Yuk tambahkan pintasan ke layar utama untuk dapat mengakses Aplikasi ELSIMIL dengan mudah </h5>
            </div>
            <div class="modal-footer p-1">
            <button type="button" class="btn btn-sm  btn-dark" onclick="closeModal()">Batal</button>
            <button type="submit" class="btn  btn-sm btn-primary" onclick="installAplikasi()"><i class="fa fa-arrow-down"></i> Install</button>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
if (typeof navigator.serviceWorker !== 'undefined') {
    navigator.serviceWorker.register('sw.js')
}
var deferredPrompt;
window.addEventListener('beforeinstallprompt', function(e) {
  console.log('beforeinstallprompt Event fired');
  e.preventDefault();

  // Stash the event so it can be triggered later.
  deferredPrompt = e;

  return false;
});

window.addEventListener('beforeinstallprompt', function(event){
    console.log('before add to home screen');
    if(!localStorage.isPopUpInstall){
        $("#modal_install_app").modal("show")
        localStorage.isPopUpInstall = true;
    }
    event.preventDefault();
    promptInstall = event;
    return false;
});

function installAplikasi() {
  // tambahkan kode ini untuk menampilkan banner add to home screen
  if(promptInstall){
    promptInstall.prompt()
    promptInstall.userChoice.then(function(choiceResult){
      console.log(choiceResult.outcome);

      if(choiceResult.outcome==='dismissed'){
        console.log('user cancelled installation');
      }else{
        console.log('user add to home screen');
      }
    });
    promptInstall = null;
  }
}
</script>
@endpush
