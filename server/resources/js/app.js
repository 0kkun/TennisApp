// require('./bootstrap');
// window.Vue = require('vue');
import './bootstrap'
import Vue from 'vue'
import FavoriteBrandComponent from './components/FavoriteBrandComponent.vue'

// 「 ExampleComponent.vue 」を 「 example-component 」という名前で使えるように読み込み
Vue.component('favorite-brand-component', require('./components/FavoriteBrandComponent.vue').default);

// const app = new Vue({
//     el: '#favorite-brand-vue',
//     components: FavoriteBrandComponent
// }).$mount('#favorite-brand-vue');

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