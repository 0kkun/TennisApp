<template>
    <div class="card mt-3 font-alegreya font-weight-bold">
        <div class="text-center pt-3 pb-2 h2 bg-secondary text-white">Brand News</div>

        <div v-if="loadStatus == false">
            <div class="h2 text-center text-danger">Now on loading .....<i class="fas fa-broadcast-tower"></i></div>
        </div>

        <transition name="fade">
            <div  v-if="loadStatus == true" class="card-columns">
                <div v-for="article in articles" :key="article.id" class="card news-card-parent">
                    <div class="card-header">
                        <span>{{ article.post_time }} up <br> from {{ article.brand_name }}</span>
                    </div>
                    <div class="card-body">
                        <div class="card-text news-text">
                            <a :href="article.url">{{ article.title }}</a>
                        </div>
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
            articles: []
        }
    },
    props:["user_id"],
    mounted: function() {
        this.fetchBrandsNews(this.user_id);
    },
    methods: {
        fetchBrandsNews: function() {
            axios.get('/api/v1/news/brands', {
                params: {
                    user_id: this.user_id,
                }
            })
            .then((response) => {
                this.articles = response.data.data;
                this.loadStatus = true;
                console.log('status;' + response.data.status);
            })
            .catch((error) => {
                console.log(error);
            });
        }
    }
}
</script>

<style lang="scss" scoped>
.news-text {
    height: 70px;
    /* テキストが多すぎる時に消す  */
    text-overflow: ellipsis;
    overflow: hidden;
}
.news-detail-button {
    width: 100px;
    background-color:cornflowerblue;
    border: none;
    cursor: pointer;
    position: absolute;
    bottom: 10px;
    left: 10px;
}
.news-card-parent {
    height: 180px;
    position: relative;
}

/* アニメーション */
.fade-enter-active, .fade-leave-active {
    transition: opacity 1s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
</style>