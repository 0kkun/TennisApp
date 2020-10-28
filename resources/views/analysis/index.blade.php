@extends('layouts.layout')

@section('content')
  @if(Auth::check())
  {{-- Chart.jsライブラリをCDNで読み込み --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

  <div class="container-fluid pt-140">
    <div class="row">

      <div class="col-sm-4 pt-3">
        <button class="btn btn-primary">開発中</button>
      </div>

      <div class="col-sm-8 pt-3">
        <div class="bg-white">
          <h1>折れ線グラフ</h1>
          <canvas id="rankingChart"></canvas>
          <script type="text/javascript" src="/js/analysis/graph.js"></script>
        </div>
      </div>

    </div>
  </div>
  @else
    @include('top.welcome')
  @endif
@endsection
