<script>
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
import { Line } from 'vue-chartjs';

export default {
    extends: Line,
    name: 'LineChart',
    data:() => ({
    chartdata: {
        labels: ["12:00", "15:00", "18:00", "21:00", "0:00", "3:00", "6:00", "9:00"],
        datasets: [
            {
                label: 'temperature',
                data: [23, 25, 22, 20, 19, 19, 19, 20, 23]
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
    }),
    mounted () {
        this.renderChart(this.chartdata, this.options);
    },
}
</script>