<div class="site-header bg-dark" style="height: 100px">
    <!------------ ハンバーガーメニュー ------------>
    <nav class="global-nav">
        <ul class="global-nav__list font-alegreya font-weight-bold">
        @if(Auth::check())
            <li class="global-nav__item"><a href="{{ route('home.index') }}">HOME</a></li>
            <li class="global-nav__item"><a href="{{ route('ranking.top') }}">Ranking</a></li>
            <li class="global-nav__item"><a href="{{ route('news.top') }}">News</a></li>
            <li class="global-nav__item"><a href="{{ route('favorite_player.top') }}">Player</a></li>
            <li class="global-nav__item"><a href="{{ route('favorite_brand.top') }}">Brand</a></li>
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
            {{-- <li class="global-nav__item"><a href="{{ route('analysis.index') }}">Player Analysis</a></li> --}}
        @else
            <li class="global-nav__item"><a href="{{ route('home.index') }}">HOME</a></li>
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
    <a href="{{ route('home.index') }}" style="text-decoration: none;">
        <div class="text-white h1 font-alegreya pt-3 pb-2 pl-5">Tennis App</div>
    </a>
</div>