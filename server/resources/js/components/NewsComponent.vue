<template>
    <div class="mt-3">
        <div class="text-center pt-3 pb-2 h3">News</div>

        <div v-if="loadStatus == false">
            <div class="h2 text-center text-danger">Now on loading .....<i class="fas fa-broadcast-tower"></i></div>
        </div>

        <div  v-if="loadStatus == true" class="card-columns">
            <div v-for="article in articles" :key="article.id" class="card news-card-parent">
                <div class="card-header">
                    <span>{{ article.post_time }} up</span>
                </div>
                <div class="card-body">
                    <div class="card-text news-text">
                        <p>{{ article.title }}</p>
                        <button class="news-detail-button text-white border-light rounded" @click="openModal">Detail</button>
                    </div>
                </div>
                <open-modal-component :url="article.url"  v-show="showContent" @close="showContent = false"></open-modal-component>
            </div>
        </div>
    </div>
</template>


<script>
import Vue from 'vue'
import OpenModalComponent from './OpenModalComponent.vue'
Vue.component('open-modal-component', require('./OpenModalComponent.vue').default);

export default {
    data() {
        return {
            showContent: false,
            loadStatus: false,
            articles: []
        }
    },
    props:["user_id"],
    mounted: function() {
        this.fetchNews(this.user_id);
    },
    methods: {
        openModal: function() {
            this.showContent = true;
        },
        closeModal: function() {
            this.showContent = false;
        },
        fetchNews: function() {
            axios.get('/api/v1/news', {
                params: {
                    user_id: this.user_id
                }
            })
            .then((response) => {
                this.articles = response.data;
                this.loadStatus = true;
                // console.log(response.data);
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
</style>