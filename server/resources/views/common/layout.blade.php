<!doctype html>
<html lang="ja">
    <head>
        <title>@yield('title') | {{ config('app.name') }}</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        {{-- フォントのライブラリ --}}
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya+Sans+SC:300">
        {{-- Fontawesome --}}
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link rel="icon" href="{{ asset('/tennis-favicon.ico') }}" type="image/x-icon">
    </head>

    <body>
        <div class="content-body-background">
            @include('common.header')
            <div class="container">
                <div class="row">

                    <div class="col-3">
                        @include('common.side-bar')
                    </div>

                    <div class="col-9">
                        @yield('content')
                    </div>

                </div>
            </div>
        </div>

        <script src="{{ mix('js/app.js') }}"></script>
        <script type="text/javascript" src="/js/common/nav_menu.js"></script>
        <script type="text/javascript" src="/js/top/scroll.js"></script>
    </body>

</html>