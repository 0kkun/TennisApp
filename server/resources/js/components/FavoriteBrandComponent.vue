<template>
    <div class="card mt-3 font-alegreya">
        <div class="text-center pt-3 h2 bg-secondary text-white">Brand</div>
        <div class="card-body">

            <div v-if="loadStatus == false">
                <div class="h2 text-center text-danger">Now on loading .....<i class="fas fa-broadcast-tower"></i></div>
            </div>

            <transition name="fade">
                <div v-if="loadStatus == true">
                    <table class="table table-striped">
                        <thead>
                            <tr class="w-100 bg-dark text-white">
                                <th class="d-table-cell d-md-table-cell border border-light" scope="col">Brand Name</th>　<!-- 常に表示 -->
                                <th class="d-table-cell d-md-table-cell border border-light" scope="col">Country</th>
                                <th class="d-table-cell d-md-table-cell border border-light" scope="col">Add / Remove</th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr v-for="brand in brands" :key="brand.id" class="border-bottom">
                                <td class="d-table-cell d-md-table-cell">{{ brand.name_jp }}</td> <!-- 常に表示 -->
                                <td class="d-table-cell d-md-table-cell">{{ brand.country }}</td>
                                <td class="d-table-cell d-md-table-cell">
                                    <div v-if="brand.is_favorited == false">
                                        <button @click.prevent="addBrand(brand.id)" class="btn btn-success pt-0 pb-0">add</button>
                                    </div>
                                    <div v-else-if="brand.is_favorited == true">
                                        <button @click.prevent="deleteBrand(brand.id)" class="btn btn-danger pt-0 pb-0">remove</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </transition>

        </div>
    </div>
</template>



<script>

export default {
    data : function () {
        return {
            brands: [],
            favorite_brand_id: '',
            loadStatus: false,
        }
    },
    props:["user_id"],
    mounted: function() {
        this.fetchBrands(this.user_id);
    },
    methods: {
        fetchBrands: function() {
            axios.get('/api/v1/brands', {
                params: {
                    user_id: this.user_id
                }
            })
            .then((response) => {
                this.brands = response.data.data;
                this.loadStatus = true;
                console.log(response.data);
            })
            .catch((error) => {
                console.log(error); 
            });
        },
        addBrand: function(brand_id) {
            axios.post('/api/v1/brands/create', {
                favorite_brand_id: brand_id,
                user_id: this.user_id
            })
            .then((response) => {
                this.brands = response.data.data;
                console.log(response.data);
            })
            .catch((error) => {
                console.log(error); 
            });
        },
        deleteBrand: function(brand_id) {
            axios.delete('/api/v1/brands/delete', {
                params: {
                    favorite_brand_id: brand_id,
                    user_id: this.user_id
                }
            })
            .then((response) => {
                this.brands = response.data.data;
                console.log(response.data);
            })
            .catch((error) => {
                console.log(error); 
            });
        }
    }
}
</script>


<style lang="scss" scoped>
td.break{
    word-break: break-all;
}
th {
    text-align: center;
    padding: 10px;
}
td {
    text-align: center;
}

/* アニメーション */
.fade-enter-active, .fade-leave-active {
    transition: opacity 1s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
</style>