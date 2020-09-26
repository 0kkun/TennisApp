<div class="top-contents-right">
  <div class="top-contents-head text-center bg-dark text-white h4 font-alegreya">ATP Ranking</div>
  <div class="top-right-list-box">
    <table class="table table-striped m-0">
      <thead>
        <tr class="thead-dark">
          <th class="top-right-table-rank-w text-center">Rank</th>
          <th class="top-right-table-name-w text-center">Name</th>
          <th class="top-right-table-point-w text-center">Point</th>
        </tr>
      </thead>
    </table>
    <!-- テーブルヘッダーのみ残してスクロールさせる -->
    <div class="top-right-tbody">
      <table class="table table-striped">
        <tbody>
          @foreach ( $atp_rankings as $ranking )
            <tr>
              <td class="top-contents-right-td top-right-table-rank-w text-center">{{ $ranking['rank'] }}</td>
              <td class="top-contents-right-td top-right-table-name-w text-center">{{ $ranking['name'] }}</td>
              <td class="top-contents-right-td top-right-table-point-w text-right">{{ $ranking['point'] }}pt</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>