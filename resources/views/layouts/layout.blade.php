<!doctype html>
<html lang="ja">
  <head>
    <title>TennisApp</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/common/reset.css">
    <link rel="stylesheet" href="/css/common/common.css">
    <link rel="stylesheet" href="/css/common/nav_menu.css">
    <link rel="stylesheet" href="/css/top/index.css">
    <link rel="stylesheet" href="/css/favorite_player/index.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya+Sans+SC:300">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
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