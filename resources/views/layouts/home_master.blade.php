<!DOCTYPE html>
<html lang="en">

<head>
  @include('components.meta')
  @include('components.style')

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script> --}}
  
</head>


<style>
  /* force semua input dan text area jadi capslock / huruf besar */
  input{
    text-transform: uppercase;
  }
  textarea{
    text-transform: uppercase;
  }

</style>
<body class="hold-transition sidebar-mini layout-fixed">

  <div class="wrapper">

    @include('layouts.navbar')

    @include('layouts.sidebar')
      <div class="content-wrapper">
        <br>
      
        <section class="content">
          {{-- @if(session("message"))
          <div class="alert alert-warning">
            {{session('message')}}
          </div>
          @endif --}}

          @if(session("status"))
            {{-- sweetalert --}}
            @include('sweetalert::alert')
          @endif

          @yield('content')
          {{-- @include('sweetalert::alert') --}}
          
        </section>
      </div>
    @include('layouts.footer')

      <aside class="control-sidebar control-sidebar-dark">
      </aside>
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

            if (/^Success/.test(sessionStatus) || /^Sukses/.test(sessionStatus) || /^success/.test(sessionStatus) || /^sukses/.test(sessionStatus)) {
                var iconData = 'success';
            } else if (/^Error/.test(sessionStatus) || /^error/.test(sessionStatus)) {
                var iconData = 'error';
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