<template>
<div>
    <div class="card mt-3 font-alegreya">
        <div class="text-center pt-3 h2 bg-secondary text-white">Player</div>

        <div class="card-body pt-2 pb-2">
            <div class="form-group p-4 h4 bg-light rounded">
                <div class="font-alegreya h4 pb-2">Search Player</div>
                <form @submit.prevent action="">
                    <input v-model="keyword" class="form-control mb-1" type="text" name="name" value="" placeholder="Please input keywords...">
                </form>
            </div>
        </div>
    </div>

    <div class="card mt-2 pt-3">
        <div v-if="loadStatus === false">
            <div class="h2 text-center text-danger">Now on loading .....<i class="fas fa-broadcast-tower"></i></div>
        </div>

        <transition name="fade">
            <div v-if="loadStatus === true" class="pr-3 pl-3">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">Name</th>
                            <th class="text-center d-none d-md-table-cell">Name(jp)</th>
                            <th class="text-center d-none d-md-table-cell">Country</th>
                            <th class="text-center d-none d-md-table-cell">Age</th>
                            <th class="text-center">Add / Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="player in players" :key="player.id">
                            <td class="text-center">{{ player.name_en }}</td>
                            <td class="text-center d-none d-md-table-cell">{{ player.name_jp }}</td>
                            <td class="text-center d-none d-md-table-cell">{{ player.country }}</td>
                            <td class="text-center d-none d-md-table-cell">{{ player.age }}</td>
                            <td v-if="player.is_favorited == false" class="text-center">
                                <button @click.prevent="createPlayer(player.id)" class="btn btn-success pt-0 pb-0 pr-1 pl-1">Add</button>
                            </td>
                            <td v-if="player.is_favorited == true" class="text-center">
                                <button @click.prevent="deletePlayer(player.id)" class="btn btn-danger pt-0 pb-0 pr-1 pl-1">Remove</button>
                            </td>
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
    data() {
        return {
            keyword: "",
            loadStatus: false,
            players: [],
            favorite_player_id: '',
        }
    },
    props:["user_id"],
    mounted: function() {
        this.fetchPlayers(this.user_id);
    },
    methods: {
        fetchPlayers: function() {
            axios.get('/api/v1/players', {
                params: {
                    user_id: this.user_id
                }
            })
            .then((response) => {
                this.players = response.data.data;
                this.loadStatus = true;
            })
            .catch((error) => {
                console.log(error); 
            });
        },
        createPlayer: function(player_id) {
            axios.post('/api/v1/players/add', {
                favorite_player_id: player_id,
                user_id: this.user_id
            })
            .then((response) => {
                this.fetchPlayers(this.user_id);
                console.log('create:' + response.data.status);
            })
            .catch((error) => {
                console.log(error); 
            });
        },
        deletePlayer: function(player_id) {
            axios.delete('/api/v1/players/delete', {
                params: {
                    favorite_player_id: player_id,
                    user_id: this.user_id
                }
            })
            .then((response) => {
                this.fetchPlayers(this.user_id);
                console.log('delete:' + response.data.status);
            })
            .catch((error) => {
                console.log(error); 
            });
        },
        searchPlayers() {
            axios.get('/api/v1/players/search', {
                params: {
                    keyword: this.keyword,
                    user_id: this.user_id,
                } 
            })
            .then(response => {
                this.players = response.data.data;
                this.loadStatus = true;
                
            })
            .catch(error => {
                console.log(error);
            });
        }
    },
    watch: {
        keyword() {
            this.searchPlayers();
            this.loadStatus = false;
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