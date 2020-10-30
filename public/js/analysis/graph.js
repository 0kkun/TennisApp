


let blue  = "rgba(0,0,255,1)";
let red   = "rgba(255,0,0,1)";
let light_pink = 'rgba(255,182,193,1)';
let black = "rgba(0,0,0,1)";
let white = "rgba(255,255,255,1)";
let clear = "rgba(0,0,0,0)";


let ctx = document.getElementById("rankingChart"); // グラフの描画位置などをビューから取得

// ***** 適当なデータを連想配列で作成 *****

let x_axis_label = ['10/1', '10/7', '10/14', '10/21', '10/28', '11/4', '11/11'];
let data_1 = [35, 34, 37, 35, 34, 35, 34, 25];
let data_2 = [2, 3, 3, 4, 3, 2, 3, 4];

let datasets_player = [
  {
    label: 'Kei Nishikori',
    data: data_1,
    borderColor: red,
    backgroundColor: clear,
    pointStyle: 'circle', // 点の形状
    pointRadius: 5, // 点の大きさ
    pointBackgroundColor: red,
  },
  {
    label: 'last year',
    data: data_2,
    borderColor: light_pink,
    backgroundColor: clear,
    pointStyle: 'circle', // 点の形状
    pointRadius: 4, // 点の大きさ
    pointBackgroundColor: light_pink,
    borderDash: [8, 2], // 破線にする。線分の長さと間隔の長さを設定
    
  }
];

// ***** データセット作成処理 *****

let data = {
  labels: x_axis_label,
  datasets: datasets_player,
};

// ***** グラフ作成処理 *****

let rankingChart = new Chart(ctx, {
  type: 'line',
  data: data,
  options: {
    title: {
      display: true,
      position:'top',
      fontSize: 17,
      text: 'ATP Rankint Trend',
    },
    elements: { line: { tension: 0 } }, // ベジェ曲線を無効にする
    responsiveAnimationDuration: 0, // サイズ変更後のアニメーションの長さ(表示速度対策)
    layout: {
      padding: {
        top:10,
        bottom:10,
        left: 10,
        right: 30
      }
    },
    legend:{ // 凡例
      labels:{
        fontSize: 12,           // 文字のサイズ
        boxWidth: 12,           // 点のサイズ
      },
    },
    scales: {
      yAxes: [{
        ticks: {
          reverse: true, //y軸の反転(1位を上にして昇順で表示)
          min: 1,  //最小値を1に
          max: 100,  //最大値を100に
          callback: function(value){
              return value;  //labelに「〜位」をつける
          }
        }
      }]
    },
  }
});




$(function(){
  // バブルチャートのデータ
  var bubleChartData = {
      datasets: [
          {
             // データ(1個目)
            data: [{"x":10 ,"y":10, "r":30} ,],
            // 色（1個目）
            backgroundColor:[ "rgb(141,63,223,0.5)" ],
            // ラベル
            label: ["test1"] 
          },
          {
              // データ(2個目)
              data: [{"x":20 ,"y":20, "r":50} ,],
              // 色（2個目）
              backgroundColor:[ "rgb(141,29,73,0.4)"],
              // ラベル（2個目）
              label: ["test2"]  
          },
          {
              // データ(3個目)
              data: [{"x":30 ,"y":30, "r":70} ,],
              // 色（3個目）
              backgroundColor:["rgb(16,230,73,0.5)"],
              // ラベル（3個目）
              label: ["test3"]  
          }
      ]};

  // オプション
  var options = {
        // タイトル
        title: {
          display: true,
          text: 'バブルチャートテスト'
        },
        // スケール
        scales: {
            // x軸
            xAxes: [{
                ticks: {max: 50, min: 0,stepSize: 10}
            }],
            // x軸
            yAxes: [{
                ticks: {max: 50,min: 0,stepSize: 10}
            }]
        },
        // tooltip
        tooltips: {
            callbacks: {
              label: function(t, d) {
                var rLabel = d.datasets[t.datasetIndex].data[t.index].r;
                return d.datasets[t.datasetIndex].label + 
                        ': (x軸:' + t.xLabel + ', y軸:' + t.yLabel + ', 円の大きさ:' + rLabel + ')';
              }
            }
        }
  };

  // コンテキストのオブジェクト
  var ctx = $("#bubblechart")[0].getContext("2d");
  // バブルチャートの描画
  var bubbleChart = new Chart(ctx, 
          {
              type: 'bubble',
              data: bubleChartData,
              options: options
          });
});