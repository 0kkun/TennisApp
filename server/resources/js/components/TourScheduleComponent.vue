<template>
    <div class="card mt-3 font-alegreya font-weight-bold">
        <div class="text-center pt-3 pb-2 h2 bg-secondary text-white">Tour Schedule</div>

        <div v-if="loadStatus == false">
            <div class="h2 text-center text-danger">Now on loading .....<i class="fas fa-broadcast-tower"></i></div>
        </div>

        <transition name="fade">
            <div v-if="loadStatus == true">
                <div class="card">
                    <table class="table table-striped">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Category</th>
                                <th>Title</th>
                                <th class="d-none d-md-table-cell">Location</th>
                                <th>Term</th>
                                <th class="d-none d-md-table-cell">Surface</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="tourSchedule in tourSchedules" :key="tourSchedule.id" class="border-bottom">
                                <td>{{ tourSchedule.category }}</td>
                                <td>{{ tourSchedule.name }}</td>
                                <td class="d-none d-md-table-cell">{{ tourSchedule.location }}</td>
                                <td>{{ tourSchedule.start_date }} ~ {{ tourSchedule.end_date }}</td>
                                <td class="d-none d-md-table-cell">{{ tourSchedule.surface }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    data : function () {
        return {
            tourSchedules: [],
            num: 5,
            loadStatus: false
        }
    },
    mounted: function() {
        this.fetchTourSchedules(this.num);
    },
    methods: {
        fetchTourSchedules: function() {
            axios.get('/api/v1/tour_schedules', { 
                params: {
                    num: this.num
                }
            })
            .then((response) => {
                this.tourSchedules = response.data.data;
                this.loadStatus = true;
                console.log('tourSchedule status:' + response.data.status);
            })
            .catch((error) => {
                console.log(error); 
            });
        }
    }
}
</script>

<style scoped>
/* アニメーション */
.fade-enter-active, .fade-leave-active {
    transition: opacity 1s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
</style>