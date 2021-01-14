<template>
    <div>
        <div class="text-center pt-3 pb-2 h3">News</div>
        <div class="card-deck">

            <div class="card">
                <div class="card-header">
                    <span>2021/01/13 up</span>
                </div>
                <div class="card-body">
                    <div class="card-text">
                        <p>ナダルのブレークポイント獲得率を分析、過去3度ツアーで1位に＜男子テニス＞</p>
                        <a class="text-white" @click="openModal" href="#"><div class="news-detail-button border-light rounded">Detail</div></a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span>2021/01/13 up</span>
                </div>
                <div class="card-body">
                    <div class="card-text">
                        <p>ナダルのブレークポイント獲得率を分析、過去3度ツアーで1位に＜男子テニス＞</p>
                        <div class="news-detail-button border-light rounded"><a class="text-white" href="#">Detail</a></div>
                    </div>
                </div>
            </div>

            <open-modal-component v-show="showContent" @close="showContent = false"></open-modal-component>

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
        }
    },
    methods: {
        openModal: function() {
            this.showContent = true;
        },
        closeModal: function() {
            this.showContent = false;
        }
    },
}
</script>

<style lang="scss" scoped>
a {
    text-decoration: none;
}
.news-detail-button {
    text-align: center;
    background-color:cornflowerblue;
    cursor: pointer;
    width: 100px;
    margin-left: auto;
    margin-right: auto;
    margin-top: 8px;
}

</style>