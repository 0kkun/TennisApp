import './bootstrap'
import Vue from 'vue'
import FavoriteBrandComponent from './components/FavoriteBrandComponent.vue'
import RankingComponent from './components/RankingComponent.vue'

// FavoriteBrandComponent.vue を <favorite-brand-component> で使えるよう読み込み
Vue.component('favorite-brand-component', require('./components/FavoriteBrandComponent.vue').default);
Vue.component('ranking-component', require('./components/RankingComponent.vue').default);

document.addEventListener('DOMContentLoaded', function() {
    // idが無い場合はVueインスタンスを作成しないようにする
    if (document.getElementById("favorite-brand-vue")) {
        const favoriteBrand = new Vue({
            el: '#favorite-brand-vue',
            components: {
                FavoriteBrandComponent,
            }
        });
    }
    if (document.getElementById("ranking-vue")) {
        const ranking = new Vue({
            el: '#ranking-vue',
            components: {
                RankingComponent,
            }
        });
    }
}, false);