<template>
    <div class="card mt-3">
        <div class="text-center pt-3 h3">BRAND</div>
        <div class="card-body">
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
                            <div v-if="brand.favorite_status == 0">
                                <button @click.prevent="createBrand(brand.id)" class="btn btn-success p-1" style="width:66px;">add</button>
                            </div>
                            <div v-else-if="brand.favorite_status == 1">
                                <button @click.prevent="deleteBrand(brand.id)" class="btn btn-danger p-1" style="width:66px;">remove</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>



<script>

export default {
    data : function () {
        return {
            brands: [],
            favorite_brand_id: '',
            updated: false,
        }
    },
    props:["user_id"],
    mounted: function() {
        this.getBrandData(this.user_id);
    },
    methods: {
        getBrandData: function() {
            axios.get('/api/get_brands_data', { params: {
                user_id: this.user_id
            }
            })
            .then((response) => {
                this.brands = response.data;
            });
        },
        createBrand: function(brand_id) {
            axios.post('/api/add_brand', {
                favorite_brand_id: brand_id,
                user_id: this.user_id
            })
            .then((response) => {
                this.updated = true;
                this.brands = response.data;
            })
            .catch( error => { console.log(error); 
            });
        },
        deleteBrand: function(brand_id) {
            axios.delete('/api/delete_brand', { params: {
                favorite_brand_id: brand_id,
                user_id: this.user_id
            }})
            .then((response) => {
                this.updated = true;
                this.brands = response.data;
            })
            .catch( error => { console.log(error); 
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
    padding-top: 20px;
}
</style>