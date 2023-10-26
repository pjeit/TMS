<!DOCTYPE html>
<html lang="en">

<head>
  @include('components.meta')
  @include('components.style')

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
</head>


<style>
  

</style>
<body class="hold-transition sidebar-mini layout-fixed">

  <div class="wrapper">

    @include('layouts.navbar')

    <!-- Main Sidebar Container -->
    @include('layouts.sidebar')
    <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <br>
      
        <!-- Main content -->
        <section class="content">

          {{-- @if(session("message"))
          <div class="alert alert-warning">
            {{session('message')}}
          </div>
          @endif --}}

          @if(session("status"))
            {{-- sweetalert --}}
            @include('sweetalert::alert')
            <div class="container-fluid">
              {{-- <div aria-labelledby="swal2-title" aria-describedby="swal2-html-container" class="radiusSendiri mt-0 mb-2 timerProgressBar swal2-popup swal2-toast swal2-icon-success swal2-show" tabindex="-1" role="alert" aria-live="polite" style="width: 100%; display: grid;">
                  <button type="button" class="swal2-close" aria-label="Close this dialog" style="display: block;">×</button>
                  <ul class="swal2-progress-steps" style="display: block;"></ul>
                  <div class="swal2-loader"></div>
                  <div class="swal2-icon swal2-success swal2-icon-show" style="display: flex;">
                      <div class="swal2-success-circular-line-left" style="background-color: rgb(255, 255, 255);"></div>
                      <span class="swal2-success-line-tip"></span>
                      <span class="swal2-success-line-long"></span>
                      <div class="swal2-success-ring"></div>
                      <div class="swal2-success-fix" style="background-color: rgb(255, 255, 255);"></div>
                      <div class="swal2-success-circular-line-right" style="background-color: rgb(255, 255, 255);"></div>
                  </div>
                  <img class="swal2-image" style="display: none;">
                  <h2 class="swal2-title" id="swal2-title" style="display: block;">
                    {{session('status')}}
                  </h2>
                  <div class="swal2-html-container" id="swal2-html-container" style="display: none;"></div>
                  <input id="swal2-input" class="swal2-input" style="display: none;">
                  <input type="file" class="swal2-file" style="display: none;">
                  <div class="swal2-range" style="display: none;"><input type="range"><output></output></div>
                  <select id="swal2-select" class="swal2-select" style="display: none;"></select>
                  <div class="swal2-radio" style="display: none;"></div>
                  <label class="swal2-checkbox" style="display: none;">
                      <input type="checkbox" id="swal2-checkbox">
                      <span class="swal2-label"></span>
                  </label>
                  <textarea id="swal2-textarea" class="swal2-textarea" style="display: none;"></textarea>
                  <div class="swal2-validation-message" id="swal2-validation-message" style="display: none;"></div>
                  <div class="swal2-actions" style="display: none;">
                      <button type="button" class="swal2-confirm swal2-styled" aria-label="" style="display: none;">OK</button>
                      <button type="button" class="swal2-deny swal2-styled" aria-label="" style="display: none;">No</button>
                      <button type="button" class="swal2-cancel swal2-styled" aria-label="" style="display: none;">Cancel</button>
                  </div>
                  <div class="swal2-footer" style="display: block;"></div>
                  <div class="swal2-timer-progress-bar-container">
                      <div class="swal2-timer-progress-bar" style="display: flex; width: 12.0588%;">
                  </div>
                </div>
              </div> --}}
              {{-- <template id="my-template">
                <swal-title>
                  Save changes to "Untitled 1" before closing?
                </swal-title>
                <swal-icon type="warning" color="red"></swal-icon>
                <swal-button type="confirm">
                  Save As
                </swal-button>
                <swal-button type="cancel">
                  Cancel
                </swal-button>
                <swal-button type="deny">
                  Close without Saving
                </swal-button>
                <swal-param name="allowEscapeKey" value="false" />
                <swal-param
                  name="customClass"
                  value='{ "popup": "my-popup" }' />
                <swal-function-param
                  name="didOpen"
                  value="popup => console.log(popup)" />
              </template>
            </div> --}}

              {{-- <div id="toast-container" class="container">
                Toast Should be here?
              </div>   --}}


            {{-- <div class="swal2-show swal2-backdrop-show swal2-icon-show" style="background-color: #fff;" role="alert">
              {{session('status')}}
            </div> --}}

          @endif

          @yield('content')
          {{-- @include('sweetalert::alert') --}}
          
        </section>
        <!-- /.content -->
      </div>
    <!-- /.content-wrapper -->
    @include('layouts.footer')

    <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
      </aside>
    <!-- /.control-sidebar -->
  </div>

@include('components.js')

  


</body>

<script>
     $(document).ready(function() {
        var sessionStatus = "<?= session()->has('status') ? session()->get('status') : null ?>";
        var sessionMsg = "<?= session()->has('msg') ? session()->get('msg') : null ?>";
        // console.log('sessionMsg', sessionMsg);
        if (sessionStatus != '') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top',
                // width: '1400px',
                timer: 2500,
                showConfirmButton: false,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
              }
            })

            if (/^Success/.test(sessionStatus) || /^Sukses/.test(sessionStatus)) {
                var iconData = 'success';
            } else if (/^Error/.test(sessionStatus)) {
                var iconData = 'danger';
            }else{
                var iconData = 'question';
            }
            Toast.fire({
                icon: iconData,
                title: sessionMsg
            })
        }
    });
</script>

</html>