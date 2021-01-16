import './bootstrap'
import Vue from 'vue'
import FavoriteBrandComponent from './components/FavoriteBrandComponent.vue'
import RankingComponent from './components/RankingComponent.vue'
import NewsComponent from './components/NewsComponent.vue'
import TourScheduleComponent from './components/TourScheduleComponent.vue'
import MovieComponent from './components/MovieComponent.vue'
import FavoritePlayerComponent from './components/FavoritePlayerComponent.vue'

// FavoriteBrandComponent.vue を <favorite-brand-component> で使えるよう読み込み
Vue.component('favorite-brand-component', require('./components/FavoriteBrandComponent.vue').default);
Vue.component('ranking-component', require('./components/RankingComponent.vue').default);
Vue.component('news-component', require('./components/NewsComponent.vue').default);
Vue.component('tour-schedule-component', require('./components/TourScheduleComponent.vue').default);
Vue.component('movie-component', require('./components/MovieComponent.vue').default);
Vue.component('favorite-player-component', require('./components/FavoritePlayerComponent.vue').default);

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
                TourScheduleComponent,
                MovieComponent
            }
        });
    }
    if (document.getElementById("favorite-player-vue")) {
        const ranking = new Vue({
            el: '#favorite-player-vue',
            components: {
                FavoritePlayerComponent
            }
        });
    }
}, false);