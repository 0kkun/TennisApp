<template>
<div class="container pt-140">
    <div class="pt-1" style="height:calc( 100vh - 200px )">
        <div class="favorite-contents-left">
            <div class="text-white bg-dark favorite-head text-center h4 font-alegreya">Brand Lists</div>
            <table class="table m-0">
                <thead class="thead-dark">
                    <th class="favorite-name-jp-w text-center">Brand Name</th>
                    <th class="favorite-country-w text-center">country</th>
                    <th class="favorite-age-w text-center">add</th>
                </thead>
            </table>
            <div class="favorite-tbody">
                <form>
                    <table class="table table-striped">
                        <tbody>
                            <tr v-for="brand in brands" :key="brand.id">
                                <td  class="favorite-td favorite-name-jp-w text-center pt-3">{{ brand.name_jp }}</td>
                                <td  class="favorite-td favorite-country-w text-center pt-3">{{ brand.country }}</td>
                                <td class="favorite-td favorite-age-w text-center pt-3">
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
                </form>
            </div>
        </div>
    </div>
</div>
</template>



<script>

export default {
    data : function () {
        return {
            brands: null,
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