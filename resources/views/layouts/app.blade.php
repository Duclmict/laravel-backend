<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}" defer></script>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

  <!-- Styles -->
  <link href="{{ asset('plugins/fontawesome/css/all.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/adminlte.css') }}" rel="stylesheet">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  @yield('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div id="app" class="wrapper">
    @include('partials.header')
    @include('partials.sidebar')
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              @yield('breadcrumb-link')
              <!-- <li class="breadcrumb-item"><a href="#">@yield('title')</a></li> -->
              <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
            </ol>
          </nav>
        </div>
      </div>
      <section class="content">
        @yield('content')
      </section>
    </div>
    @include('partials.footer')
  </div>

  <script src="{{ asset('plugins/momentjs/moment.min.js') }}"></script>
  <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/adminlte.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>
  @yield('scripts')
</body>

</html>