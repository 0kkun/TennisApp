<div class="top-contents-left">
  <div class="top-contents-head text-center bg-dark text-white h4 font-alegreya">Player News</div>
  <div class="top-left-list-box">
    @if ( !empty($news_articles) )
      <ul>
        @foreach ( $news_articles as $index => $news_article )
          @if ( $index % 2 === 0 )
            @if ( $news_article['post_time'] == $today->format('Y-m-d') )
              <li class="top-left-li pl-1 bg-gray font-weight-bold"><a href="{{ $news_article['url'] }}" id="open">{{ $news_article['title'] }} - {{$news_article['post_time']}} <span class="text-danger">new!</span></a></li>
            @else
              <li class="top-left-li pl-1 bg-gray"><a href="{{ $news_article['url'] }}" id="open">{{ $news_article['title'] }} - {{$news_article['post_time']}}</a></li>
            @endif
          @else
            @if ( $news_article['post_time'] == $today->format('Y-m-d') )
              <li class="top-left-li pl-1 bg-light font-weight-bold"><a href="{{ $news_article['url'] }}" id="open">{{ $news_article['title'] }} - {{$news_article['post_time']}} <span class="text-danger">new!</span></a></li>
            @else
              <li class="top-left-li pl-1 bg-light"><a href="{{ $news_article['url'] }}" id="open">{{ $news_article['title'] }} - {{$news_article['post_time']}}</a></li>
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
    <ul>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
      <li class="top-left-li">≫ <a href="#">News 1</a></li>
    </ul>
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