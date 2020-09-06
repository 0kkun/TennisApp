<!doctype html>
<html lang="ja">
  <head>
    <title>TennisApp</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya+Sans+SC:300">
  </head>

  <body>
    <header>
        <!------------ ハンバーガーメニュー ------------>
      <nav class="global-nav">
        <ul class="global-nav__list font-alegreya font-weight-bold">
          <li class="global-nav__item"><a href="">HOME</a></li>
          <li class="global-nav__item"><a href="">Register</a></li>
          <li class="global-nav__item"><a href="">Login</a></li>
          <li class="global-nav__item"><a href="">Logout</a></li>
          <li class="global-nav__item"><a href="">Favorite Player</a></li>
          <li class="global-nav__item"><a href="">Favorite Brand</a></li>
        </ul>
      </nav>
      <div class="hamburger" id="js-hamburger">
        <span class="hamburger__line hamburger__line--1"></span>
        <span class="hamburger__line hamburger__line--2"></span>
        <span class="hamburger__line hamburger__line--3"></span>
      </div>
      <div class="black-bg" id="js-black-bg"></div>
      <!------------------------------------------->
      <a href="#" style="text-decoration: none;">
        <span class="text-white h1 font-alegreya"> Tennis App </span>
        <span class="text-primary h4 font-alegreya pl-2"> - Integrate Infomation - </span>
      </a>
    </header>

    <main>
      <!-- トップリンクバー -->
      <div class="container-fluid top-link">
        <div class="row text-center bg-dark">
          <!-- <div class="col-4 pt-2 pb-2"><a class="text-white h5 font-alegreya" href="#">News</a></div>
          <div class="col-4 pt-2 pb-2"><a class="text-white h5 font-alegreya" href="#">Movie</a></div>
          <div class="col-4 pt-2 pb-2"><a class="text-white h5 font-alegreya" href="#">Rank & Schedule</a></div> -->
        </div>
      </div>

      <div class="container-fluid">
        <div class="row">
          <!-- --------------------- left contents --------------------- -->
          <div class="col-sm-4 pt-3">
            <div class="contents-left">
              <div class="contents-head text-center bg-dark text-white h4 font-alegreya">Player News</div>
              <div class="list-box">
                <ul>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                </ul>
              </div>
            </div>

            <div class="contents-left mt-3">
              <div class="contents-head text-center bg-dark text-white h4 font-alegreya">Item News</div>
              <div class="list-box">
                <ul>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                  <li>≫ <a href="#">News 1</a></li>
                </ul>
              </div>
            </div>

            <div class="contents-left mt-3">
              <div class="contents-head text-center bg-dark text-white h4 font-alegreya">Racket Impression</div>
              <div class="list-box movie-box">
                <iframe width="100%" height="250" src="https://www.youtube.com/embed/2zhpKrbp6Lk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <iframe width="100%" height="250" src="https://www.youtube.com/embed/2zhpKrbp6Lk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <iframe width="100%" height="250" src="https://www.youtube.com/embed/2zhpKrbp6Lk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
              </div>
            </div>

          </div>

          <!-- --------------------- center contents --------------------- -->
          <div class="col-sm-4 pt-3">
            <div class="contents-center">
              <div class="contents-head text-center bg-dark text-white h4 font-alegreya">Tour Info</div>
              <div class="list-box tour-box">
                <table class="table table-striped ">
                  <thead>
                    <tr class="thead-dark">
                      <th>Date</th>
                      <th>Name</th>
                      <th>Category</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>2020/01/01</td>
                      <td>US Open</td>
                      <td>1500pt</td>
                    </tr>
                    <tr>
                      <td>2020/01/01</td>
                      <td>US Open</td>
                      <td>1500pt</td>
                    </tr>
                    <tr>
                      <td>2020/01/01</td>
                      <td>US Open</td>
                      <td>1500pt</td>
                    </tr>
                    <tr>
                      <td>2020/01/01</td>
                      <td>US Open</td>
                      <td>1500pt</td>
                    </tr>
                    <tr>
                      <td>2020/01/01</td>
                      <td>US Open</td>
                      <td>1500pt</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="contents-center mt-3">
              <div class="contents-head text-center bg-dark text-white h4 font-alegreya">Movie</div>
              <div class="list-box movie-box">
                <iframe width="100%" height="250" src="https://www.youtube.com/embed/MfEGqgZffUo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <iframe width="100%" height="250" src="https://www.youtube.com/embed/MfEGqgZffUo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <iframe width="100%" height="250" src="https://www.youtube.com/embed/MfEGqgZffUo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
              </div>
            </div>
          </div>

          <!-- --------------------- right contents --------------------- -->
          <div class="col-sm-4 pt-3">
            <div class="contents-right">
              <div class="contents-head text-center bg-dark text-white h4 font-alegreya">ATP Ranking</div>
              <div class="list-box">
                <table class="table table-striped ">
                  <thead>
                    <tr class="thead-dark">
                      <th>Rank</th>
                      <th>Name</th>
                      <th>Point</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>1</td>
                      <td>ジョコビッチ</td>
                      <td>1500pt</td>
                    </tr>
                    <tr>
                      <td>2</td>
                      <td>ナダル</td>
                      <td>1500pt</td>
                    </tr>
                    <tr>
                      <td>3</td>
                      <td>ティエム</td>
                      <td>1500pt</td>
                    </tr>
                    <tr>
                      <td>4</td>
                      <td>フェデラー</td>
                      <td>1500pt</td>
                    </tr>
                    <tr>
                      <td>5</td>
                      <td>メドヴェージェフ</td>
                      <td>1500pt</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
    </main>

    <footer>
      <div class="small text-center text-muted pt-2"> Copyright©shinji. All Rights Reserved.</div>
    </footer>
    <script type="text/javascript" src="js/header.js"></script>
  </body>
</html>