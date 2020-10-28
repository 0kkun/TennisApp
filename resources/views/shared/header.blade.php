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
        <li class="global-nav__item"><a href="{{ route('favorite_player.index') }}">Favorite Player</a></li>
        <li class="global-nav__item"><a href="{{ route('favorite_brand.index') }}">Favorite Brand</a></li>
        <li class="global-nav__item"><a href="{{ route('analysis.index') }}">Player Analysis</a></li>
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
    <a class="text-white font-20 font-alegreya top-link-tab p-1" href="{{ route('favorite_player.index') }}">Favorite Player</a>
    <a class="text-white font-20 font-alegreya top-link-tab p-1" href="{{ route('favorite_brand.index') }}">Favorite Brand</a>
    <a class="text-white font-20 font-alegreya top-link-tab p-1" href="{{ route('analysis.index') }}">Player Analysis</a>
    @endif
  </div>

</header>