<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="serviceUploadFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Service Upload</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <CPFormServiceUpload @refreshServiceUploadForm="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Services</h3>
                    <div class="card-tools">
                        <div class="input-group" style="width: 350px;">
                            <input type="text" name="query" v-model="query" class="form-control float-right" placeholder="Search">

                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" @click="getAllInitials"><i class="fas fa-search"></i></button>
                                <select class="form-control" v-model="status" @change="getAllInitials">
                                    <option value="all">All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <button type="button" class="btn btn-default" @click="uploadServices"><i class="fas fa-file-upload text-dark"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 600px;">
                    <CPDetailServiceList :services="services.data" @refreshServiceList="getAllInitials()" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="services.per_page != null ? services.per_page : 52" :records="services.total != null ? services.total : 550" >
                    </pagination>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            query: '',
            services:   {data: [], total: 0,},
            status: '',
            type: '',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        closeModals(){
            $('#uploadServiceFormModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            this.closeModals();
            axios.get('/api/consultant_practices/services?status='+this.status+'&query='+this.query)
            .then(response => {
                this.services = response.data.services ?? {data: [], total: 0,};
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Services did not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        uploadServices(){
            this.loading = true;
            $('#uploadServiceFormModal').modal('show');
            this.loading = false;
        }
    },
}
</script>