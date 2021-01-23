<script>

let lightPurple = "rgba(141,63,223,0.5)";
let lightRed = "rgba(141,29,73,0.4)";
let lightGreen = "rgba(16,230,73,0.5)";
let lightBlue = "rgba(0,0,255,0.5)";

let age = {
    0:10,
    1:20,
    2:30,
    3:40,
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
    props: ["analysisData"],
    extends: Bubble,
    name: 'Bubble',
    data:() => ({
        chartdata: [],
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
                        max: 40,
                        stepSize: 10,
                        callback: function(value) {
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
                            ' : ( X[ Age ]:' + t.xLabel + ', Y[ Rank Ave ]:' + t.yLabel + ', Size[ num ]:' + rLabel + ' )';
                    }
                }
            }
        },
    }),
    methods: {
        inputValue: function(data){
            this.chartdata = {
                datasets: [
                    {
                        // データ(0個目) X軸が年齢、Y軸が平均年齢、円の大きさが人数
                        data: [{"x":age[0], "y":data.average_rank['10s'], "r":data.count_player['10s']} ,],
                        backgroundColor: lightBlue,
                        label: ["10s table"] 
                    },
                    {
                        // データ(1個目)
                        data: [{"x":age[1] ,"y":data.average_rank['20s'], "r":data.count_player['20s']} ,],
                        backgroundColor: lightPurple,
                        label: ["20s table"] 
                    },
                    {
                        // データ(2個目)
                        data: [{"x":age[2] , "y":data.average_rank['30s'], "r":data.count_player['30s']} ,],
                        backgroundColor: lightRed,
                        label: ["30s table"]  
                    },
                    {
                        // データ(3個目)
                        data: [{"x":age[3] ,"y":data.average_rank['40s'], "r":data.count_player['40s']} ,],
                        backgroundColor: lightGreen,
                        label: ["40s table"]  
                    }
                ]
            }
        }
    },
    mounted () {
        this.inputValue(this.analysisData);
        this.renderChart(this.chartdata, this.options);
    },
}
</script>