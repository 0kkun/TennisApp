<template>
    <div class="card mt-3 font-alegreya font-weight-bold">
        <div class="text-center pt-3 pb-2 h2 bg-secondary text-white">Player Movie</div>

        <div v-if="loadStatus == false">
            <div class="h2 text-center text-danger">Now on loading .....<i class="fas fa-broadcast-tower"></i></div>
        </div>

        <transition name="fade">
            <div v-if="loadStatus == true" class="card-columns w-100">
                <div v-for="movie in movies" :key="movie.id" class="card">
                    <div class="card-header p-2">
                        <span>{{ movie.post_time }} up</span>
                    </div>
                    <div class="card-body p-0">
                        <iframe width="100%" height="177" :src="movie.url" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loadStatus: false,
            movies: []
        }
    },
    props:["user_id"],
    mounted: function() {
        this.fetchMovies(this.user_id);
    },
    methods: {
        fetchMovies: function() {
            axios.get('/api/v1/player_movies', {
                params: {
                    user_id: this.user_id
                }
            })
            .then((response) => {
                this.movies = response.data;
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
/* アニメーション */
.fade-enter-active, .fade-leave-active {
    transition: opacity 1s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
</style>