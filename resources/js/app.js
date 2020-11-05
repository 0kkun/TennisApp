
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
// 「 ExampleComponent.vue 」を 「 example-component 」という名前で使えるように読み込み
// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// const app = new Vue({
//     el: '#app'
// });


const app = new Vue({
  el: '#app',
  data: {
    players: [] // 表示用の配列を用意
  },
  methods: {
    fetchPlayers: function() {
      axios.get('/api/axios_test/get')
      .then((response)=>{
        this.players = response.data;
      })
      .catch(function (error) {
        console.log(error);
      })
    },
    addFavoritePlayer: function(player_id) {
      axios.post('/api/axios_test/add',{ id: player_id })
      .then((response) => {
        alert('add complete!!');
        this.players = response.data
      })
      .catch((error) => {
        //Error: Request failed with status code 500
        alert(error);
      });
    }
  },
  // インスタンス生成時に fetchPlayers()を実行したいので、created フックに登録
  created() { 
    this.fetchPlayers()
  },
});
