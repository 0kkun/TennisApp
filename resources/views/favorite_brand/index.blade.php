@extends('layouts.layout')

@section('content')
  @if(Auth::check())

    <div class="favorite-player-wrapper container pt-140">

      <div class="pt-3">
        <div class="favorite-contents-left">
          <div class="text-white bg-dark favorite-head text-center h4 font-alegreya">Brand Lists</div>
          <table class="table m-0">
            <thead class="thead-dark">
              <th class="favorite-name-jp-w text-center">Brand Name</th>
              <th class="favorite-country-w text-center">country</th>
              <th class="favorite-age-w text-center">add</th>
            </thead>
          </table>
          <div class="favorite-tbody">
            <table class="table table-striped">
              <tbody>
                @if ( !empty($brand_lists) )
                  @foreach ($brand_lists as $brand)
                    <tr>
                      <td class="favorite-td favorite-name-jp-w text-center pt-3">{{ $brand['name_jp'] }} ( {{$brand['name_en']}} )</td>
                      <td class="favorite-td favorite-country-w text-center pt-3">{{ $brand['country'] }}</td>
                      <td class="favorite-td favorite-age-w text-center pt-3">

                        @if ( $brand['favorite_status'] == 0 )
                          <form method="post" action="{{ route('favorite_brand.add') }}">
                            @csrf
                            <input type="hidden" name="favorite_brand_id" value="{{ $brand['id'] }}">
                            <button type="submit" class="btn btn-success p-1" style="width:66px;">add</button>
                          </form>
                        @else
                          <form method="post" action="{{ route('favorite_brand.remove') }}">
                            @csrf
                            <input type="hidden" name="favorite_brand_id" value="{{ $brand['id'] }}">
                            <button type="submit" class="btn btn-danger p-1" style="width:66px;">remove</button>
                          </form>
                        @endif

                      </td>
                    </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
      <div>
    </div>
  @else
    @include('top.welcome')
  @endif
@endsection


