<!DOCTYPE html>
<html lang="en">

<head>
  @include('components.meta')
  @include('components.style')

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
</head>


<style>
  [type=checkbox] {
  width: 1rem;
  height: 1rem;
  color: dodgerblue;
  vertical-align: middle;
  -webkit-appearance: none;
  background: none;
  border: 0;
  outline: 0;
  flex-grow: 0;
  background-color: #FFFFFF;
  transition: background 300ms;
  cursor: pointer;
}

/* Pseudo element for check styling */
[type=checkbox]::before {
  content: "";
  color: transparent;
  display: block;
  width: inherit;
  height: inherit;
  border-radius: inherit;
  border: 0;
  background-color: transparent;
  background-size: contain;
  box-shadow: inset 0 0 0 1px #CCD3D8;
}


/* Checked */

[type=checkbox]:checked {
  background-color: currentcolor;
}

[type=checkbox]:checked::before {
  box-shadow: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E %3Cpath d='M15.88 8.29L10 14.17l-1.88-1.88a.996.996 0 1 0-1.41 1.41l2.59 2.59c.39.39 1.02.39 1.41 0L17.3 9.7a.996.996 0 0 0 0-1.41c-.39-.39-1.03-.39-1.42 0z' fill='%23fff'/%3E %3C/svg%3E");
}


/* Disabled */

[type=checkbox]:disabled {
  background-color: #CCD3D8;
  opacity: 0.84;
  cursor: not-allowed;
}


/* IE */

[type=checkbox]::-ms-check {
  content: "";
  color: transparent;
  display: block;
  width: inherit;
  height: inherit;
  border-radius: inherit;
  border: 0;
  background-color: transparent;
  background-size: contain;
  box-shadow: inset 0 0 0 1px #CCD3D8;
}

[type=checkbox]:checked::-ms-check {
  box-shadow: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E %3Cpath d='M15.88 8.29L10 14.17l-1.88-1.88a.996.996 0 1 0-1.41 1.41l2.59 2.59c.39.39 1.02.39 1.41 0L17.3 9.7a.996.996 0 0 0 0-1.41c-.39-.39-1.03-.39-1.42 0z' fill='%23fff'/%3E %3C/svg%3E");
}
    /* styling dropdown */
    /* .content {
      padding: 7rem 0; }

    h2 {
      font-size: 20px; }

    .custom-dropdown .btn:active, .custom-dropdown .btn:focus {
      -webkit-box-shadow: none !important;
      box-shadow: none !important;
      outline: none; }

    .custom-dropdown .btn.btn-custom {
      border: 1px solid #efefef; }

    .custom-dropdown .dropdown-link {
      color: #888;
      font-size: 15px;
      display: inline-block;
      padding: 8px 15px;
      background: #f8f9fa;
      position: relative; }
      .custom-dropdown .dropdown-link:after {
        content: ""; }

    .custom-dropdown .dropdown-item {
      font-size: 14px;
      color: #888;
      border-bottom: 1px solid #efefef;
      padding-top: 10px;
      padding-left: 15px;
      padding-bottom: 10px;
      position: relative; }
      .custom-dropdown .dropdown-item:before {
        content: "";
        position: absolute;
        width: 0px;
        height: 100%;
        left: 0;
        bottom: 0;
        top: 0;
        opacity: 0;
        visibility: hidden;
        z-index: 2;
        background: #007bff;
        -webkit-transition: .3s all ease;
        -o-transition: .3s all ease;
        transition: .3s all ease; }
      .custom-dropdown .dropdown-item:last-child {
        border-bottom: none; }
      .custom-dropdown .dropdown-item:hover {
        color: #000;
        padding-left: 20px; }
      .custom-dropdown .dropdown-item:hover:before {
          opacity: 1;
          visibility: visible;
          width: 6px; }

    .custom-dropdown .dropdown-menu {
      border: 1px solid transparent;
      -webkit-box-shadow: 0 15px 30px 0 rgba(0, 0, 0, 0.2);
      box-shadow: 0 15px 30px 0 rgba(0, 0, 0, 0.2);
      margin-top: 0px !important;
      padding-top: 0;
      padding-bottom: 0;
      opacity: 0;
      left: 0 !important;
      -webkit-transition: .3s margin-top ease, .3s opacity ease, .3s visibility ease;
      -o-transition: .3s margin-top ease, .3s opacity ease, .3s visibility ease;
      transition: .3s margin-top ease, .3s opacity ease, .3s visibility ease;
      visibility: hidden; }
      .custom-dropdown .dropdown-menu.active {
        opacity: 1;
        visibility: visible;
        margin-top: 20px !important; }

    .custom-dropdown.show .dropdown-link {
      color: #fff;
      background: #007bff; } */
  /* end of it */

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
        var sessionMessage = "<?= session()->has('status') ? session()->get('status') : null ?>";
        if (sessionMessage != '') {
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

            if (/^Success/.test(sessionMessage) || /^Sukses/.test(sessionMessage)) {
                var iconData = 'success';
                var titleData = 'Data tersimpan!';
            } else if (/^Error/.test(sessionMessage)) {
                var iconData = 'danger';
                var titleData = 'Data gagal disimpan!';
              }else{
                var iconData = 'success';
                var titleData = '';
            }
            Toast.fire({
                icon: iconData,
                title: titleData
            })
        }
    });
</script>

</html>