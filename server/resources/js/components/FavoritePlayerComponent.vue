<template>
    <div class="card mt-3 font-alegreya font-weight-bold">
        <div class="text-center pt-3 h2">Player</div>

        <div class="card-body pt-0 pb-2">
            <div class="form-group p-4 h4 bg-light rounded">
                <div class="font-alegreya h4 pb-2">Search Player</div>
                <form action="">
                    <input class="form-control mb-1" type="text" name="name" value="" placeholder="Please input keywords...">
                </form>
            </div>
        </div>

        <div class="pr-3 pl-3">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center">Name</th>
                        <th class="text-center">Country</th>
                        <th class="text-center">Age</th>
                        <th class="text-center">Add / Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">Kei Nishikori</td>
                        <td class="text-center">JPN</td>
                        <td class="text-center">31</td>
                        <td class="text-center"><button class="btn btn-success pt-0 pb-0">Add</button></td>
                    </tr>
                    <tr>
                        <td class="text-center">Roger Federer</td>
                        <td class="text-center">JPN</td>
                        <td class="text-center">31</td>
                        <td class="text-center"><button class="btn btn-danger pt-0 pb-0">Remove</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<script>
export default {
    
}
</script>

<style lang="scss" scoped>


</style>