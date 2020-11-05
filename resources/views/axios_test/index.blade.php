<!doctype html>
  <html lang="{{ app()->getLocale() }}">
    <head>
      <meta charset="utf-8">
      <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- ←① -->
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- ←② -->
      <title>Laravel-Vue-todo</title>
    </head>
    <body>
      <div id="app">
        <div class="container">
          <div class="row">
            <div class="col-xs-12">
              <br>
            </div>
            <div class="col-xs-6">


              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>プレイヤー名</th>
                    <th>Player Name</th>
                    <th>Country</th>
                    <th>addボタン</th>
                    <th>removeボタン</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="player in players" v-bind:key="player.id">
                    <td>@{{ player.id }}</td>
                    <td>@{{ player.name_jp }}</td>
                    <td>@{{ player.name_en }}</td>
                    <td>@{{ player.country }}</td>
                    <td><button class="btn btn-primary" name="player_id" v-on:click="addFavoritePlayer(player.id)">add</button></td>
                    <td><button class="btn btn-danger" name="player_id" v-on:click="removeFavoritePlayer(player.id)">remove</button></td>
                  </tr>
                </tbody>
              </table>


            </div>
            <div class="col-xs-6">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="タスクを入力してください">
                <span class="input-group-btn">
                  <button class="btn btn-success" type="button">タスクを登録</button>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <script src="{{ asset('js/app.js') }}"></script> <!-- ←④ -->
    </body>
</html>