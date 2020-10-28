@extends('layouts.layout')

@section('content')
  @if(Auth::check())
  <div class="container pt-140">
    <div class="bg-white">
      <h1>折れ線グラフ</h1>
      <canvas id="myLineChart"></canvas>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
    
      <script>
        var ctx = document.getElementById("myLineChart");
        var myLineChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: ['8月1日', '8月2日', '8月3日', '8月4日', '8月5日', '8月6日', '8月7日'],
            datasets: [
              {
                label: '最高気温(度）',
                data: [35, 34, 37, 35, 34, 35, 34, 25],
                borderColor: "rgba(255,0,0,1)",
                backgroundColor: "rgba(0,0,0,0)"
              },
              {
                label: '最低気温(度）',
                data: [25, 27, 27, 25, 26, 27, 25, 21],
                borderColor: "rgba(0,0,255,1)",
                backgroundColor: "rgba(0,0,0,0)"
              }
            ],
          },
          options: {
            title: {
              display: true,
              text: '気温（8月1日~8月7日）'
            },
            scales: {
              yAxes: [{
                ticks: {
                  suggestedMax: 40,
                  suggestedMin: 0,
                  stepSize: 10,
                  callback: function(value, index, values){
                    return  value +  '度'
                  }
                }
              }]
            },
          }
        });
      </script>
    </div>
  </div>
  @else
    @include('top.welcome')
  @endif
@endsection
