<template>
<div class="mt-3">
    <!-- タブ切り替え -->
    <div class="tab d-flex">
        <div @click="change('Ranking')" :class="{'active': isActive === 'Ranking'}" class="tab-btn border rounded-top pt-1 pb-1 pl-3 pr-3">Ranking</div>
        <div @click="change('Chart')" :class="{'active': isActive === 'Chart'}" class="tab-btn border rounded-top pt-1 pb-1 pl-3 pr-3">Chart</div>
    </div>

    <!-- ランキングタブ -->
    <div v-if="isActive === 'Ranking'" class="border font-alegreya font-weight-bold">
        <div class="text-center pt-2 pb-2 h2 bg-secondary text-white">Ranking <small>( at {{ ymd }})</small></div>
        <div class="card-body">

            <div v-if="loadStatus == false">
                <div class="h2 text-center text-danger">Now on loading .....<i class="fas fa-broadcast-tower"></i></div>
            </div>

            <transition name="fade">
                <div v-if="loadStatus == true">
                    <table class="table table-striped">
                        <thead>
                            <tr class="w-100 bg-dark text-white">
                                <th class="d-table-cell d-md-table-cell border border-light" scope="col">Rnk</th>　<!-- 常に表示 -->
                                <th class="d-none d-md-table-cell border border-light" scope="col">Rank Change</th>
                                <th class="d-none d-md-table-cell border border-light" scope="col">Most High</th>
                                <th class="d-table-cell d-md-table-cell border border-light" scope="col">Pt</th>　<!-- 常に表示 -->
                                <th class="d-none d-md-table-cell border border-light" scope="col">Pt Change</th>
                                <th class="d-table-cell d-md-table-cell border border-light" scope="col">Name</th>　<!-- 常に表示 -->
                                <th class="d-none d-md-table-cell border border-light" scope="col">Age</th>
                                <th class="d-table-cell d-md-table-cell border border-light" scope="col">Country</th>
                                <th class="d-none d-md-table-cell border border-light tour" scope="col">Current Tour</th>
                                <th class="d-none d-md-table-cell border border-light tour" scope="col">Pre Tour</th>
                                <th class="d-none d-md-table-cell border border-light" scope="col">Next Pt</th>
                                <th class="d-none d-md-table-cell border border-light" scope="col">Max Pt</th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr v-for="ranking in rankings" :key="ranking.id" class="border-bottom">
                                <td class="d-table-cell d-md-table-cell">{{ ranking.rank }}</td> <!-- 常に表示 -->
                                <td class="d-none d-md-table-cell">{{ ranking.rank_change }}</td>
                                <td class="d-none d-md-table-cell">{{ ranking.most_highest }}</td>
                                <td class="d-table-cell d-md-table-cell">{{ ranking.point }}</td> <!-- 常に表示 -->
                                <td class="d-none d-md-table-cell">{{ ranking.point_change }}</td>
                                <td class="d-table-cell d-md-table-cell">{{ ranking.name_en }}</td> <!-- 常に表示 -->
                                <td class="d-none d-md-table-cell">{{ ranking.age }}</td>
                                <td class="d-table-cell d-md-table-cell">{{ ranking.country }}</td>
                                <td class="d-none d-md-table-cell">{{ ranking.current_tour_result_en }}</td>
                                <td class="d-none d-md-table-cell">{{ ranking.pre_tour_result_en }}</td>
                                <td class="d-none d-md-table-cell">{{ ranking.next_point }}</td>
                                <td class="d-none d-md-table-cell">{{ ranking.max_point }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </transition>
        </div>
    </div>

    <!-- グラフタブ -->
    <div v-else-if="isActive === 'Chart'" class="border font-alegreya font-weight-bold mb-2">
        <div class="text-center pt-2 pb-2 h2 bg-secondary text-white">Analysis Chart</div>
        <div class="border mb-2 mr-2 ml-2">
            <RankingChartComponent :height="250" :analysisData="analysisData"></RankingChartComponent>
        </div>
    </div>

</div>
</template>

<script>

import RankingChartComponent from './RankingChartComponent.vue'

export default {
    data: function () {
        return {
            rankings: [],
            analysisData: [],
            num: 100,
            analysisNum: 100,
            loadStatus: false,
            isActive: 'Ranking',
            ymd: '',
        }
    },
    components: { 
        RankingChartComponent
    },
    mounted: function() {
        this.fetchRankings(this.num);
        this.fetchAnalysis(this.num);
    },
    methods: {
        change: function(tabName){
            this.isActive = tabName
        },
        fetchRankings: function() {
            axios.get('/api/v1/rankings', { 
                params: {
                    num: this.num
                }
            })
            .then((response) => {
                this.rankings = response.data.data;
                this.ymd = response.data.data[0].ymd;
                this.loadStatus = true;
                console.log('ranking-status:' + response.data.status);
            })
            .catch((error) => {
                console.log(error); 
            });
        },
        fetchAnalysis: function() {
            axios.get('/api/v1/analysis_age', { 
                params: {
                    num: this.analysisNum,
                }
            })
            .then((response) => {
                this.analysisData = response.data.data;
                console.log('chartData-status:' + response.data.status);
            })
            .catch((error) => {
                console.log(error); 
            });
        }
    }
}
</script>

<style lang="scss" scoped>
td.break{
    word-break: break-all;
}
th, td {
    text-align: center;
    padding: 5px;
}
td.tour, th.tour {
    min-width:120px;
}
/* アニメーション */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
.tab-btn {
    cursor: pointer;
}
.tab div.active {
    background-color: rgb(79, 81, 208);
    color: white;
}
</style>