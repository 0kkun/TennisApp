@extends('layouts.layout')

@section('content')
    @if(Auth::check())

    <div class="container-fluid pt-140">
        <div class="row">

        <div class="col-sm-4 pt-3">
            <div class="form-group p-4 h4 bg-light rounded" style="height:350px;">
            <div class="font-alegreya h4 pb-2 font-weight-bold">Search Criteria</div>

            <form action="" method="GET">
                <div class="font-16 font-weight-bold mb-2">▶︎ Player Name</div>
                <select class="form-control mb-3" name="graph">
                <option value=""> All </option>
                <option value="">錦織 圭</option>
                </select>

                <div class="font-16 font-weight-bold mb-2">▶︎ Select Duration</div>
                <div class="row">
                <div class="col-3">
                    <div class="text-left font-16 p-2" style="line-height:24px;">From:</div>
                    <div class="text-left font-16 p-2" style="line-height:24px;">To:</div>
                </div>
                <div class="col-9">
                    <input class="form-control mb-1" type="text" name="ranking_start_date" id="datepicker_start">
                    <input class="form-control" type="text" name="ranking_end_date" id="datepicker_end">
                </div>
                </div>
                <div class="text-right">
                <button class="btn btn-primary mt-3" type="submit">Select</button>
                </div>
            </form>
            </div>
        </div>

        <div class="col-sm-8 pt-3">
            <div class="bg-white rounded">
            <canvas id="rankingChart"></canvas>
            </div>

            <div class="bg-white rounded mt-3">
            <canvas id="bubblechart"></canvas>
            </div>
        </div>

        {{-- Chart.jsライブラリをCDNで読み込み --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
        <script type="text/javascript" src="/js/analysis/graph.js"></script>
        </div>
    </div>

    {{-- <div id="app">
        <example-component test="GET DATA: {{ 'AAA' }}"></example-component>
    </div>
    <script src=" {{ mix('js/app.js') }} "></script> --}}
    
    @else
        @include('top.welcome')
    @endif
@endsection
