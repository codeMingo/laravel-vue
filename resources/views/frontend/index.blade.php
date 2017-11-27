<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ mix('css/frontend/vendor.css') }}">
    <link rel="stylesheet" href="{{ mix('css/frontend/app.css') }}">
    <script type="text/javascript" src="/plupload-2.1.3/js/plupload.full.min.js"></script>
  </head>
  <body>
    <div id="app">
      <router-view></router-view>
    </div>

    <script src="{{ mix('js/frontend/app.js') }}"></script>
  </body>
</html>
