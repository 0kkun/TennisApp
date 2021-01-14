<div class="site-header bg-dark" style="height: 100px">
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
        <div class="text-white h1 font-alegreya pt-3 pb-2 pl-5">Tennis App</div>
    </a>
</div>