@extends('layouts.layout')

@section('content')
  @if(Auth::check())

    <!---------------- モーダル用 ------------------->
    <div id="mask" class="hidden"></div>
    <!-- モーダル画面 -->
    <div id="modal" class="hidden">
      <div class="modal-conteiner">
        <div class="modal-head">
          <div class="h3 pl-2">Favorite Brand機能の使い方</div>
          <button class="btn btn-danger text-right" id="close">X</button>
        </div>
        <div class="modal-body">
          <p>お気に入りブランドを検索し、登録することでトップページで表示する情報を絞り込むことができます。</p>
        </div>
      </div>
    </div>
    <!--------------------------------------------->

    <div class="favorite-player-wrapper container-fluid pt-140">

      <div class="row">

        <div class="col-sm-5 pt-5">
          <div class="form-group p-4 h4 bg-light rounded" style="height:300px;">
            <div class="font-alegreya h4 pb-2">Search Player<a class="pl-3 pt-1 fas fa-question-circle question-btn" id="open"></a></div>
            <div class="row">

              <div class="col-3">
                <div class="text-left font-16 p-2" style="line-height:24px;">Brand Name:</div>
                <div class="text-left font-16 p-2" style="line-height:24px;">Country:</div>
              </div>

              <div class="col-9">


              </div>
            </div>

          </div>
        </div>

        <div class="col-sm-7 pt-3 pr-4">
          <div class="favorite-contents-left">
            <div class="text-white bg-dark favorite-head text-center h4 font-alegreya">Player Lists</div>
            <table class="table m-0">
              <thead class="thead-dark">
                <th class="favorite-name-jp-w text-center">Brand Name</th>
                <th class="favorite-country-w text-center">country</th>
                <th class="favorite-age-w text-center">age</th>
                <th class="favorite-age-w text-center">add</th>
              </thead>
            </table>
            <div class="favorite-tbody">
              <table class="table table-striped">
                <tbody>

                </tbody>
              </table>
            </div>
          </div>
        </div>


      </div>
    </div>
  @else
    @include('top.welcome')
  @endif
@endsection


