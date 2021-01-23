<script>

let lightPurple = "rgba(141,63,223,0.5)";
let lightRed = "rgba(141,29,73,0.4)";
let lightGreen = "rgba(16,230,73,0.5)";
let lightBlue = "rgba(0,0,255,0.5)"

let averageAge = {
    0:10,
    1:20,
    2:30,
    3:40,
};

let averageRank = {
    0:78,
    1:25,
    2:43,
    3:85,
};

// ここでチャートの種類を選択
/**
 * Bar       : 棒グラフ
 * Line      : 折れ線グラフ
 * Doughnut  : ドーナツグラフ
 * Pie       : 円グラフ
 * Radar     : レーダーグラフ
 * Polararea : 極域グラフ
 * Bubble    : バブルグラフ
 * Scatter   : 散布図（mixinでは未対応とのこと）
 **/
import { Bubble } from 'vue-chartjs';

export default {
    extends: Bubble,
    name: 'Bubble',
    data:() => ({
        chartdata: {
            datasets: [
                {
                    // データ(0個目)
                    data: [{"x":averageAge[0] ,"y":averageRank[0], "r":20} ,],
                    backgroundColor: lightBlue,
                    label: ["10s table"] 
                },
                {
                    // データ(1個目)
                    data: [{"x":averageAge[1] ,"y":averageRank[1], "r":40} ,],
                    backgroundColor: lightPurple,
                    label: ["20s table"] 
                },
                {
                    // データ(2個目)
                    data: [{"x":averageAge[2] , "y":averageRank[2], "r":50} ,],
                    backgroundColor: lightRed,
                    label: ["30s table"]  
                },
                {
                    // データ(3個目)
                    data: [{"x":averageAge[3] ,"y":averageRank[3], "r":30} ,],
                    backgroundColor: lightGreen,
                    label: ["40s table"]  
                }
            ]
        },
        options: {
            title: {
                display: true,
                position:'top',
                fontSize: 17,
                text: 'Generation Analysis *at latest ranking'
            },
            // スケール
            scales: {
                // x軸 : 年齢
                xAxes: [{
                    ticks: {
                        min: 0,
                        max: 50,
                        stepSize: 10,
                        callback: function(value){
                            return value + 's';  //labelに「〜位」とかをつけれる
                        }
                    },
                    // x軸のラベル
                    scaleLabel: {
                        display: true,
                        fontSize: 12,
                        labelString: "Age",
                    },
                }],
                // y軸 : ランキング
                yAxes: [{
                    // y軸のラベル
                    scaleLabel: {
                        display: true,
                        fontSize: 12,
                        labelString: "Ranking"
                    },
                    ticks: {
                        reverse: true,
                        max: 100,
                        min: 1,
                        stepSize: 20
                    }
                }]
            },
            // tooltip
            tooltips: {
                callbacks: {
                    label: function(t, d) {
                    let rLabel = d.datasets[t.datasetIndex].data[t.index].r;
                    return d.datasets[t.datasetIndex].label + 
                            ': (x軸:' + t.xLabel + ', y軸:' + t.yLabel + ', 円の大きさ:' + rLabel + ')';
                    }
                }
            }
        },
    }),
    mounted () {
        this.renderChart(this.chartdata, this.options);
    },
}
</script>