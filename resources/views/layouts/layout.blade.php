<!doctype html>
<html lang="ja">
  <head>
    <title>TennisApp</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/nav_menu.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya+Sans+SC:300">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/scroll.js"></script>
    <script type="text/javascript" src="js/welcome.js"></script>
  </head>


  <body>
    <header class="site-header">
      <!------------ ハンバーガーメニュー ------------>
      <nav class="global-nav">
        <ul class="global-nav__list font-alegreya font-weight-bold">
          @if(Auth::check())
            <li class="global-nav__item"><a href="/">HOME</a></li>
            <li class="global-nav__item">
              <a class="dropdown-item" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                Logout
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
              </form>
            </li>
            <li class="global-nav__item"><a href="">Favorite Player</a></li>
            <li class="global-nav__item"><a href="">Favorite Brand</a></li>
            <li class="global-nav__item"><a href="">Player Analysis</a></li>
          @else
            <li class="global-nav__item"><a href="/">HOME</a></li>
            <li class="global-nav__item"><a href="{{ route('register') }}">Register</a></li>
            <li class="global-nav__item"><a href="{{ route('login') }}">Login</a></li>
          @endif
        </ul>
      </nav>
      <div class="hamburger" id="js-hamburger">
        <span class="hamburger__line hamburger__line--1"></span>
        <span class="hamburger__line hamburger__line--2"></span>
        <span class="hamburger__line hamburger__line--3"></span>
      </div>
      <div class="black-bg" id="js-black-bg"></div>
      <!------------------------------------------->
      <a href="/" style="text-decoration: none;">
        <span class="text-white h1 font-alegreya"> Tennis App </span>
        <span class="text-info h4 font-alegreya pl-2"> - Integrate Infomation - </span>
      </a>
      <!-- トップリンクバー -->
      <div class="top-link rounded text-center bg-info w-100 ">
        @if(Auth::check())
        <a class="text-white font-20 font-alegreya top-link-tab p-1" href="/">Home</a>
        <a class="text-white font-20 font-alegreya top-link-tab p-1" href="#">Favorite Player</a>
        <a class="text-white font-20 font-alegreya top-link-tab p-1" href="#">Favorite Brand</a>
        <a class="text-white font-20 font-alegreya top-link-tab p-1" href="#">Player Analysis</a>
        @endif
      </div>

    </header>

    <main>
      @yield('content')
    </main>

    <footer>
      <div class="small text-center text-muted pt-2">
        テニス大好き星人が作成したテニス情報統合サイトです！ <br>
        Copyright©shinji. All Rights Reserved.
      </div>
    </footer>
    <script type="text/javascript" src="js/nav_menu.js"></script>
  </body>
</html>