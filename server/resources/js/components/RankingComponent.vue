<template>
    <div class="card mt-3">
        <div class="text-center pt-3 h3">Ranking</div>
        <div class="card-body">
            <table>
                <thead>
                    <tr class="w-100 bg-dark text-white">
                        <th class="d-table-cell d-sm-table-cell border border-light" scope="col">Rnk</th>　<!-- 常に表示 -->
                        <th class="d-none d-sm-table-cell border border-light" scope="col">Rank Change</th>
                        <th class="d-none d-sm-table-cell border border-light" scope="col">Most High</th>
                        <th class="d-table-cell d-sm-table-cell border border-light" scope="col">Pt</th>　<!-- 常に表示 -->
                        <th class="d-none d-sm-table-cell border border-light" scope="col">Pt Change</th>
                        <th class="d-table-cell d-sm-table-cell border border-light" scope="col">Name</th>　<!-- 常に表示 -->
                        <th class="d-none d-sm-table-cell border border-light" scope="col">Age</th>
                        <th class="d-table-cell d-sm-table-cell border border-light" scope="col">Country</th>
                        <th class="d-none d-sm-table-cell border border-light" scope="col">Current Tour</th>
                        <th class="d-none d-sm-table-cell border border-light" scope="col">Pre Tour</th>
                        <th class="d-none d-sm-table-cell border border-light" scope="col">Next Pt</th>
                        <th class="d-none d-sm-table-cell border border-light" scope="col">Max Pt</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-bottom">
                        <td class="d-table-cell d-sm-table-cell">1</td> <!-- 常に表示 -->
                        <td class="d-none d-sm-table-cell" >+5</td>
                        <td class="d-none d-sm-table-cell" >4</td>
                        <td class="d-table-cell d-sm-table-cell">4300</td> <!-- 常に表示 -->
                        <td class="d-none d-sm-table-cell">-2</td>
                        <td class="d-table-cell d-sm-table-cell">Kei Nishikori</td> <!-- 常に表示 -->
                        <td class="d-none d-sm-table-cell">31</td>
                        <td class="d-table-cell d-sm-table-cell">JPN</td>
                        <td class="d-none d-sm-table-cell">-</td>
                        <td class="d-none d-sm-table-cell">-</td>
                        <td class="d-none d-sm-table-cell">250</td>
                        <td class="d-none d-sm-table-cell">1500</td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="d-table-cell d-sm-table-cell">1</td> <!-- 常に表示 -->
                        <td class="d-none d-sm-table-cell" >+5</td>
                        <td class="d-none d-sm-table-cell" >4</td>
                        <td class="d-table-cell d-sm-table-cell">4300</td> <!-- 常に表示 -->
                        <td class="d-none d-sm-table-cell">-2</td>
                        <td class="d-table-cell d-sm-table-cell">Kei Nishikori</td> <!-- 常に表示 -->
                        <td class="d-none d-sm-table-cell">31</td>
                        <td class="d-table-cell d-sm-table-cell">JPN</td>
                        <td class="d-none d-sm-table-cell">-</td>
                        <td class="d-none d-sm-table-cell">-</td>
                        <td class="d-none d-sm-table-cell">250</td>
                        <td class="d-none d-sm-table-cell">1500</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <ul v-for="ranking in rankings" :key="ranking.id">
            <li>{{ ranking.name }}</li>
        </ul>
    </div>
</template>

<script>
export default {
    data : function () {
        return {
            rankings: [
                {
                    id: 1,
                    rank: 1,
                    rank_change: +1,
                    most_high: 4,
                    point: 4300,
                    point_change: -2,
                    name: 'Kei Nshikori',
                    age: 31,
                    country: 'JPN',
                    current_tour: '-',
                    pre_tour: '-',
                    next_point: 250,
                    max_point: 1500
                },
                {
                    id: 2,
                    rank: 1,
                    rank_change: +1,
                    most_high: 4,
                    point: 4300,
                    point_change: -2,
                    name: 'Shinji Okumoto',
                    age: 31,
                    country: 'JPN',
                    current_tour: '-',
                    pre_tour: '-',
                    next_point: 250,
                    max_point: 1500
                },
            ]
        }
    },
}
</script>

<style lang="scss" scoped>
td.break{
    word-break: break-all;
}
th, td {
    text-align: center;
    padding: 5px;
}
</style>