<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="uploadFile">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Select Excel/CSV File</label>
                    <input type="file" class="form-control" accept=".xlsx,.xls,.csv" @change="handleFile">
                </div>
            </div>
            <div class="col-md-12">
                <div v-if="uploadResult" class="alert alert-info">
                    <p>Successful Uploads: <strong>{{ uploadResult.successful }}</strong></p>

                    <div v-if="uploadResult.errors.length">
                        <hr>
                        <h6>Errors</h6>
                        <ul>
                            <li v-for="(error,index) in uploadResult.errors" :key="index">Row {{ error.row }} : {{ error.message }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <button class="btn btn-primary" :disabled="loading"> Upload</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
            file: null,
            uploadConsultantServiceData: new Form({
                consultant_id: '',
                file: null
            }),
            uploadResult: null
        }
    },

    methods: {
        handleFile(event){
            this.file = event.target.files[0];
        },

        async uploadFile(){
            if(!this.file){
                return $this.$toast.fire({
                    icon:'error',
                    title:'Please select a file'
                });
            }
            this.loading = true;
            this.uploadConsultantServiceData.file = this.file;
            this.uploadConsultantServiceData.consultant_id = this.consultant_id;
            this.uploadConsultantServiceData.post('/api/consultant_practices/consultant_services/import')
            .then(response => {
                this.uploadResult = response.data.data;

                this.$swal.fire({
                    icon: 'success',
                    title: 'The Company details has been modified',
                    showConfirmButton: false,
                    timer: 1500
                });
                this.$emit('refreshServiceList', response);
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            })
            .finally(()=>{
                this.loading = false;
            });
        }
    },
    props: {
        consultant_id: {type: [String, Number], required: true},
    }
}
</script>