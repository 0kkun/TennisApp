import './bootstrap'
import Vue from 'vue'
import FavoriteBrandComponent from './components/FavoriteBrandComponent.vue'
import RankingComponent from './components/RankingComponent.vue'
import PlayersNewsComponent from './components/PlayersNewsComponent.vue'
import BrandsNewsComponent from './components/BrandsNewsComponent.vue'
import TourScheduleComponent from './components/TourScheduleComponent.vue'
import PlayerMovieComponent from './components/PlayerMovieComponent.vue'
import BrandMovieComponent from './components/BrandMovieComponent.vue'
import FavoritePlayerComponent from './components/FavoritePlayerComponent.vue'
import HamburgerMenuComponent from './components/HamburgerMenuComponent.vue'


Vue.component('favorite-brand-component', require('./components/FavoriteBrandComponent.vue').default);
Vue.component('ranking-component', require('./components/RankingComponent.vue').default);
Vue.component('players-news-component', require('./components/PlayersNewsComponent.vue').default);
Vue.component('brands-news-component', require('./components/BrandsNewsComponent.vue').default);
Vue.component('tour-schedule-component', require('./components/TourScheduleComponent.vue').default);
Vue.component('player-movie-component', require('./components/PlayerMovieComponent.vue').default);
Vue.component('brand-movie-component', require('./components/BrandMovieComponent.vue').default);
Vue.component('favorite-player-component', require('./components/FavoritePlayerComponent.vue').default);
Vue.component('hamburger-menu-component', require('./components/HamburgerMenuComponent.vue').default);


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
                PlayersNewsComponent,
                BrandsNewsComponent,
            }
        });
    }
    if (document.getElementById("home-vue")) {
        const ranking = new Vue({
            el: '#home-vue',
            components: {
                TourScheduleComponent,
                PlayerMovieComponent,
                BrandMovieComponent
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
    if (document.getElementById("hamburger-menu-vue")) {
        const ranking = new Vue({
            el: '#hamburger-menu-vue',
            components: {
                HamburgerMenuComponent
            }
        });
    }
}, false);