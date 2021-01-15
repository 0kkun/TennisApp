<template>
    <div class="card mt-3">
        <div class="text-center pt-3 h3">RANKING</div>
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
                                <th class="d-none d-md-table-cell border border-light" scope="col">Current Tour</th>
                                <th class="d-none d-md-table-cell border border-light" scope="col">Pre Tour</th>
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
                                <td class="d-table-cell d-md-table-cell">{{ ranking.name }}</td> <!-- 常に表示 -->
                                <td class="d-none d-md-table-cell">{{ ranking.age }}</td>
                                <td class="d-table-cell d-md-table-cell">{{ ranking.country }}</td>
                                <td class="d-none d-md-table-cell">{{ ranking.current_tour_result }}</td>
                                <td class="d-none d-md-table-cell">{{ ranking.pre_tour_result }}</td>
                                <td class="d-none d-md-table-cell">{{ ranking.next_point }}</td>
                                <td class="d-none d-md-table-cell">{{ ranking.max_point }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
export default {
    data : function () {
        return {
            rankings: [],
            num: 100,
            loadStatus: false
        }
    },
    mounted: function() {
        this.fetchRankings(this.num);
    },
    methods: {
        fetchRankings: function() {
            axios.get('/api/v1/rankings', { 
                params: {
                    num: this.num
                }
            })
            .then((response) => {
                this.rankings = response.data;
                this.loadStatus = true;
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

/* アニメーション */
.fade-enter-active, .fade-leave-active {
    transition: opacity 1s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
</style>