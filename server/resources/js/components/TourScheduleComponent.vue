<template>
    <div>
        ツアー情報
    </div>
</template>

<script>
export default {
    data : function () {
        return {
            tourSchedules: [],
            num: 10,
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
                this.tourSchedules = response.data;
                this.loadStatus = true;
                console.log(this.tourSchedules);
            })
            .catch((error) => {
                console.log(error); 
            });
        }
    }
}
</script>

<style scoped>

</style>