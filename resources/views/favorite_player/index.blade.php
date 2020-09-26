@extends('layouts.layout')

@section('content')
  @if(Auth::check())
    <div class="favorite-player-wrapper container-fluid pt-140">

      <div class="row">

        <div class="col-sm-5 pt-5 pl-5">
          <div class="form-group p-4 h4 bg-light rounded" style="height:300px;">
            <div class="font-alegreya h4 pb-2">Search Player</div>
            <div class="row">

              <div class="col-3">
                <div class="text-left font-16 p-2" style="line-height:24px;">Name:</div>
                <div class="text-left font-16 p-2" style="line-height:24px;">Country:</div>
                <div class="text-left font-16 p-2" style="line-height:24px;">Age:</div>
              </div>

              <div class="col-9">
                <form action="{{ route( 'favorite_player.index') }}" method="GET">
                  @csrf
                  <input class="form-control mb-1" type="text" name="name" value="{{ $params['name_jp'] ?? '' }}" plceholder="Please input keywords...">

                  <select class="form-control mb-1" name="country">
                    <option value=""> All </option>
                    @foreach ( $country_names as $key => $country )
                      <option value="{{ $country }}"> {{ $country }} </option>
                    @endforeach
                  </select>

                  <select class="form-control mb-1" name="age">
                    <option value=""> All </option>
                    <option value="19">under 20</option>
                    <option value="20">over 20</option>
                    <option value="30">over 30</option>
                    <option value="40">over 40</option>
                  </select>

                  <div class="text-right">
                    <button class="btn btn-primary mt-1" type="submit">Search</button>
                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>

        <div class="col-sm-7 pt-3 pr-4">
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
                    <td class="favorite-td favorite-name-jp-w text-center"><a href="{{ $player['wiki_url'] }}">{{ $player['name_jp'] }}</a></td>
                    <td class="favorite-td favorite-name-en-w text-center"><a href="{{ $player['wiki_url'] }}">{{ $player['name_en'] }}</a></td>
                    <td class="favorite-td favorite-country-w text-center">{{ $player['country'] }}</td>
                    <td class="favorite-td favorite-age-w text-center">{{ $player['age'] }}</td>

                    <td class="favorite-td favorite-age-w text-center">
                    @if ( $player['favorite_status'] == 0 )
                      <form method="post" action="{{ route('favorite_player.add') }}">
                        @csrf
                        <input type="hidden" name="favorite_player_id" value="{{ $player['id'] }}">
                        <button type="submit" class="favorite-add-btn bg-success text-white rounded p-1" style="width:66px;">add</button>
                      </form>
                    @else
                      <form method="post" action="{{ route('favorite_player.remove') }}">
                        @csrf
                        <input type="hidden" name="favorite_player_id" value="{{ $player['id'] }}">
                        <button type="submit" class="favorite-add-btn bg-danger text-white rounded p-1" style="width:66px;">remove</button>
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
  @else
    @include('top.welcome')
  @endif
@endsection


