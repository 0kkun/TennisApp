<!doctype html>
<html lang="ja">
  <head>
    <title>TennisApp</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{-- Bootstrap4.2 --}}
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous"> --}}
    {{-- フォントのライブラリ --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya+Sans+SC:300">
    {{-- Fontawesome --}}
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">

    {{-- Vue.jsを使用してajax通信する時のため --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js')}}"></script>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    {{-- datepicker --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/js/common/datepick.js"></script>

    <link rel="stylesheet" href="/css/common/reset.css">
    <link rel="stylesheet" href="/css/common/common.css">
    <link rel="stylesheet" href="/css/common/nav_menu.css">
    <link rel="stylesheet" href="/css/common/modal.css">
    <link rel="stylesheet" href="/css/top/index.css">
    <link rel="stylesheet" href="/css/favorite_player/index.css">
    <script type="text/javascript" src="/js/top/scroll.js"></script>
    <script type="text/javascript" src="/js/common/welcome.js"></script>

  </head>

  <body>
    @include('shared.header')

    <main>
      @yield('content')
    </main>

    @include('shared.footer')
    <script type="text/javascript" src="/js/common/nav_menu.js"></script>
  </body>
</html>