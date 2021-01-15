import './bootstrap'
import Vue from 'vue'
import FavoriteBrandComponent from './components/FavoriteBrandComponent.vue'
import RankingComponent from './components/RankingComponent.vue'
import NewsComponent from './components/NewsComponent.vue'
import TourComponent from './components/TourComponent.vue'
import MovieComponent from './components/MovieComponent.vue'

// FavoriteBrandComponent.vue を <favorite-brand-component> で使えるよう読み込み
Vue.component('favorite-brand-component', require('./components/FavoriteBrandComponent.vue').default);
Vue.component('ranking-component', require('./components/RankingComponent.vue').default);
Vue.component('news-component', require('./components/NewsComponent.vue').default);
Vue.component('tour-component', require('./components/TourComponent.vue').default);
Vue.component('movie-component', require('./components/MovieComponent.vue').default);

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
    if (document.getElementById("news-vue")) {
        const ranking = new Vue({
            el: '#news-vue',
            components: {
                NewsComponent,
            }
        });
    }
    if (document.getElementById("home-vue")) {
        const ranking = new Vue({
            el: '#home-vue',
            components: {
                TourComponent,
                MovieComponent
            }
        });
    }
}, false);