import './bootstrap'
import Vue from 'vue'
import FavoriteBrandComponent from './components/FavoriteBrandComponent.vue'

// FavoriteBrandComponent.vue を <favorite-brand-component> で使えるよう読み込み
Vue.component('favorite-brand-component', require('./components/FavoriteBrandComponent.vue').default);

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
}, false);