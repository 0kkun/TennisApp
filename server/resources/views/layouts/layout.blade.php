<!doctype html>
<html lang="ja">
    <head>
        <title>TennisApp</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        {{-- Vue.jsを使用してajax通信する時のため --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- npm run devコマンドで生成した「app.css」と「app.js」を読み込んでいる --}}
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script src="{{ asset('js/app.js')}}"></script>

        {{-- フォントのライブラリ --}}
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya+Sans+SC:300">
        {{-- Fontawesome --}}
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">

        {{-- Bootstrap4.2で上書き TODO: ちゃんとライブラリをインストールする --}}
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    
        {{-- datepicker --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    </head>

    <body>
        @include('shared.header')

        <main>
        @yield('content')
        </main>

        @include('shared.footer')

        <script type="text/javascript" src="/js/common/nav_menu.js"></script>
        <script type="text/javascript" src="/js/top/scroll.js"></script>
    </body>
</html>