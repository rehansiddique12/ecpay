<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token()}}">
    {{-- <link rel="icon" type="image/png" sizes="16x16" href="{{ getFile(config('location.logoIcon.path').'favicon.png')}}"> --}}
    <title>@lang($basic->site_title) | @yield('title')</title>
    <link href="{{asset('assets/admin/css/style.min.css')}}" rel="stylesheet">
    <style>
    /* Full-page loader styling */
    #loader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    /* Loader animation */
    .spinner {
      border: 8px solid #f3f3f3; /* Light grey */
      border-top: 8px solid #3498db; /* Blue */
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: spin 1s linear infinite;
    }

    /* Keyframes for spinning animation */
    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }
  </style>
   @stack('styles')
</head>

<body>
    <div id="loader">
    <div class="spinner"></div>
  </div>
  {{ $slot }}



    <script>
    // Hide loader and show content after the page fully loads
    window.addEventListener("load", () => {
      const loader = document.getElementById("loader");
      const content = document.getElementById("content");

      // Hide the loader
      loader.style.display = "none";

      // Show the content
      content.style.display = "block";
    });
  </script>

@stack('js')
</body>
</html>
