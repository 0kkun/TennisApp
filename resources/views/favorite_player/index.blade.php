@extends('layouts.layout')

@section('content')
  @if(Auth::check())

    <!---------------- モーダル用 ------------------->
    <div id="mask" class="hidden"></div>
    <!-- モーダル画面 -->
    <div id="modal" class="hidden">
      <div class="modal-conteiner">
        <div class="modal-head">
          <div class="h3 pl-2">Favorite Player機能の使い方</div>
          <button class="btn btn-danger text-right" id="close">X</button>
        </div>
        <div class="modal-body">
          <p>お気に入り選手を検索し、登録することでトップページで表示する情報を絞り込むことができます。</p>
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
                <div class="text-left font-16 p-2" style="line-height:24px;">Name:</div>
                <div class="text-left font-16 p-2" style="line-height:24px;">Country:</div>
                <div class="text-left font-16 p-2" style="line-height:24px;">Age:</div>
              </div>

              <div class="col-9">
                <form action="{{ route( 'favorite_player.index') }}" method="GET">
                  @csrf
                  <input class="form-control mb-1" type="text" name="name" value="{{ $params['name'] ?? '' }}" plceholder="Please input keywords...">

                  <select class="form-control mb-1" name="country">
                    @if ( empty($params['country']) )
                      <option value=""> All </option>
                      @foreach ( $country_names as $key => $country )
                        <option value="{{ $country }}"> {{ $country }} </option>
                      @endforeach
                    @else
                      <option value="{{ $params['country'] }}"> {{ $params['country']}} </option>
                      <option value=""> All </option>
                      @foreach ( $country_names as $key => $country )
                        <option value="{{ $country }}"> {{ $country }} </option>
                      @endforeach
                    @endif
                  </select>

                  <select class="form-control mb-1" name="age">
                      @if ( $params['age'] == 19 )
                        <option value="19">under 20</option>
                        <option value=""> All </option>
                        <option value="20">Between 20 ~ 29</option>
                        <option value="30">Between 30 ~ 39</option>
                        <option value="40">over 40</option>
                      @elseif ( $params['age'] == 20 )
                        <option value="20">Between 20 ~ 29</option>
                        <option value=""> All </option>
                        <option value="19">under 20</option>
                        <option value="30">Between 30 ~ 39</option>
                        <option value="40">over 40</option>
                      @elseif ( $params['age'] == 30 )
                        <option value="30">Between 30 ~ 39</option>
                        <option value=""> All </option>
                        <option value="19">under 20</option>
                        <option value="20">Between 20 ~ 29</option>
                        <option value="40">over 40</option>
                      @elseif ( $params['age'] == 40 )
                        <option value="40">over 40</option>
                        <option value=""> All </option>
                        <option value="19">under 20</option>
                        <option value="20">Between 20 ~ 29</option>
                        <option value="30">Between 30 ~ 39</option>
                      @else
                        <option value=""> All </option>
                        <option value="19">under 20</option>
                        <option value="20">Between 20 ~ 29</option>
                        <option value="30">Between 30 ~ 39</option>
                        <option value="40">over 40</option>
                      @endif
                  </select>

                  <div class="text-right">
                    <button class="btn btn-primary mt-1" type="submit">Search</button>
                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>

        <div class="col-sm-7 pr-4">

            <!-- フラッシュメッセージ -->
            <div style="height:45px;">
                @if (session('flash_success'))
                    <div class="flash-js bg-success text-center text-white rounded py-2 my-0">
                        {{ session('flash_success') }}
                    </div>
                @elseif (session('flash_alert'))
                    <div class="flash-js bg-danger text-center text-white rounded py-2 my-0">
                        {{ session('flash_alert') }}
                    </div>
                @endif
                <script type="text/javascript" src="/js/common/flash-message.js"></script>
            </div>

          <div class="favorite-contents-left">
            <div class="text-white bg-dark favorite-head text-center h4 font-alegreya">Player Lists</div>
            <table class="table m-0">
              <thead class="thead-dark">
                <th class="favorite-name-jp-w text-center">Name(jp)</th>
                <th class="favorite-name-en-w text-center">Name(en)</th>
                <th class="favorite-country-w text-center">country</th>
                <th class="favorite-age-w text-center">age</th>
                <th class="favorite-age-w text-center">add</th>
              </thead>
            </table>
            <div class="favorite-tbody">
              <table class="table table-striped">
                <tbody>
                @foreach ($player_lists as $player)
                  <tr>
                    <td class="favorite-td favorite-name-jp-w text-center pt-3"><a href="{{ $player['wiki_url'] }}">{{ $player['name_jp'] }}</a></td>
                    <td class="favorite-td favorite-name-en-w text-center pt-3"><a href="{{ $player['wiki_url'] }}">{{ $player['name_en'] }}</a></td>
                    <td class="favorite-td favorite-country-w text-center pt-3">{{ $player['country'] }}</td>
                    <td class="favorite-td favorite-age-w text-center pt-3">{{ $player['age'] }}</td>

                    <td class="favorite-td favorite-age-w text-center">
                    @if ( $player['favorite_status'] == 0 )
                      <form method="post" action="{{ route('favorite_player.add') }}">
                        @csrf
                        <input type="hidden" name="favorite_player_id" value="{{ $player['id'] }}">
                        <button type="submit" class="btn btn-success p-1" style="width:66px;">add</button>
                      </form>
                    @else
                      <form method="post" action="{{ route('favorite_player.remove') }}">
                        @csrf
                        <input type="hidden" name="favorite_player_id" value="{{ $player['id'] }}">
                        <button type="submit" class="btn btn-danger p-1" style="width:66px;">remove</button>
                      </form>
                    @endif
                    </td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>


      </div>
    </div>
    <script type="text/javascript" src="/js/common/modal.js"></script>
  @else
    @include('top.welcome')
  @endif
@endsection


