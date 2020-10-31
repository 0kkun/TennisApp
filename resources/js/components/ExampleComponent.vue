<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        I'm an example component.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.test {
    color: red;
}
</style>


<script>
    export default {
      // Laravel からのデータを受け取り
      props: {
          test: String,
      },
      mounted() {
          console.log('Component mounted.')
      }
    }
</script>
