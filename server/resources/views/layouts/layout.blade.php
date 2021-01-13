<!doctype html>
<html lang="ja">
    <head>
        <title>TennisApp</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        {{-- Vue.jsを使用してajax通信する時のため --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- npm run devコマンドで生成した「app.css」と「app.js」を読み込んでいる --}}
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <script src="{{ mix('js/app.js') }}" defer></script>

        {{-- フォントのライブラリ --}}
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya+Sans+SC:300">
        {{-- Fontawesome --}}
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">

        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> --}}

        {{-- jQuery 3系のCDN --}}
        {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css"> --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        {{-- Chart.jsライブラリをCDNで読み込み --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
    </head>

    <body>
        <div class="content-body">
            @include('shared.header')

            <main>
                @yield('content')
            </main>

            @include('shared.footer')
            <script type="text/javascript" src="/js/common/nav_menu.js"></script>
            <script type="text/javascript" src="/js/top/scroll.js"></script>
        </div>
    </body>
</html>