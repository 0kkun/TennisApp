<div class="top-contents-left">
  <div class="top-contents-head text-center bg-dark text-white h4 font-alegreya">Player News</div>
  <div class="top-left-list-box">
    @if ( !empty($news_articles) )
      <ul>
        @foreach ( $news_articles as $index => $news_article )
          @if ( $index % 2 === 0 )
            @if ( $news_article['post_time'] == $today->format('Y-m-d') )
              <li class="top-left-li pl-1 bg-gray">
                <a class="font-weight-bold" href="{{ $news_article['url'] }}">{{ $news_article['title'] }}</a>
                <span>- {{$news_article['post_time']}}</span>
                <span class="text-danger font-weight-bold">new!</span>
            </li>
            @else
              <li class="top-left-li pl-1 bg-gray">
                <a href="{{ $news_article['url'] }}">{{ $news_article['title'] }}</a>
                <span>- {{$news_article['post_time']}}</span>
              </li>
            @endif
          @else
            @if ( $news_article['post_time'] == $today->format('Y-m-d') )
              <li class="top-left-li pl-1 bg-light">
                <a class="font-weight-bold" href="{{ $news_article['url'] }}">{{ $news_article['title'] }}</a>
                <span>- {{$news_article['post_time']}}</span>
                <span class="text-danger font-weight-bold">new!</span>
              </li>
            @else
              <li class="top-left-li pl-1 bg-light">
                <a href="{{ $news_article['url'] }}">{{ $news_article['title'] }}</a>
                <span>- {{$news_article['post_time']}}</span>
              </li>
            @endif
          @endif
        @endforeach
      </ul>
      <!-- ページネーション -->
      <div class="p-3 text-center">
        {{ $news_articles->appends((request()->query()))->links() }}
      </div>
    @endif
  </div>
</div>

<div class="top-contents-left mt-3">
  <div class="top-contents-head text-center bg-dark text-white h4 font-alegreya">Item News</div>
  <div class="top-left-list-box">
    @if ( !empty($brand_news_articles) )
      <ul>
        @foreach ( $brand_news_articles as $index => $brand_news_article )
          @if ( $index % 2 === 0 )
            @if ( $brand_news_article['post_time'] == $today->format('Y-m-d') )
              <li class="top-left-li pl-1 bg-gray">
                <span class="bg-success rounded font-14 text-white m-1">{{ $brand_news_article['brand_name'] }}</span>
                <a class="font-weight-bold" href="{{ $brand_news_article['url'] }}">{{ $brand_news_article['title'] }} </a>
                <span> - {{$brand_news_article['post_time']}}</span>
                <span class="text-danger">new!</span>
              </li>
            @else
              <li class="top-left-li pl-1 bg-gray">
                <span class="bg-success rounded font-14 text-white m-1">{{ $brand_news_article['brand_name'] }}</span>
                <a href="{{ $brand_news_article['url'] }}">{{ $brand_news_article['title'] }}</a>
                <span> - {{$brand_news_article['post_time']}}</span>
              </li>
            @endif
          @else
            @if ( $brand_news_article['post_time'] == $today->format('Y-m-d') )
              <li class="top-left-li pl-1 bg-light">
                <span class="bg-success rounded font-14 text-white m-1">{{ $brand_news_article['brand_name'] }}</span>
                <a class="font-weight-bold" href="{{ $brand_news_article['url'] }}">{{ $brand_news_article['title'] }}</a>
                <span> - {{$brand_news_article['post_time']}}</span>
                <span class="text-danger font-weight-bold">new!</span>
              </li>
            @else
              <li class="top-left-li pl-1 bg-light">
                <span class="bg-success rounded font-14 text-white m-1">{{ $brand_news_article['brand_name'] }}</span>
                <a href="{{ $brand_news_article['url'] }}">{{ $brand_news_article['title'] }}</a>
                <span> - {{$brand_news_article['post_time']}}</span>
              </li>
            @endif
          @endif
        @endforeach
      </ul>
      <!-- ページネーション -->
      <div class="p-3 text-center">
        {{ $brand_news_articles->appends((request()->query()))->links() }}
      </div>
    @endif
  </div>
</div>

<div class="top-contents-left mt-3">
  <div class="top-contents-head text-center bg-dark text-white h4 font-alegreya">Racket Impression</div>
  <div class="top-left-list-box top-movie-box">
    <iframe width="100%" height="250" src="https://www.youtube.com/embed/2zhpKrbp6Lk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <iframe width="100%" height="250" src="https://www.youtube.com/embed/2zhpKrbp6Lk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <iframe width="100%" height="250" src="https://www.youtube.com/embed/2zhpKrbp6Lk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
  </div>
</div>