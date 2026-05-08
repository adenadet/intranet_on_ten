<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="serviceFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Service Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <CPFormService :editMode.sync="editMode" :service.sync="service" @refreshServiceForm="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap table-striped ">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>Specialty</th>
                <th>Description</th>
                <th>Status</th>
                <th><button class="btn btn-sm btn-primary float-right" @click="addService"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="services.length > 0">
            <tr v-for="(service,index) in services">
                <td>{{ addOne(index) }}</td>
                <td>{{ service.name }}</td>
                <td>{{ service.specialty?.name || 'General' }}</td>
                <td :title="service.description" v-html="readMore(service.description, 50, '...')"></td>
                <td><span v-if="service.status === 1" class="badge badge-success">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="btn btn-block dropdown-item" @click="viewService(service)"><i class="fas fa-eye mr-2 text-success"></i> View Service</button>
                        <button class="btn btn-block dropdown-item" @click="updateService(service)"><i class="fas fa-edit mr-2 text-primary"></i> Update Service</button>
                        <button class="btn btn-block dropdown-item" @click="deactivateService(service.id)"><i class="fas fa-times mr-2 text-danger"></i> {{service.status == 1 ? 'Deactivate' : 'Reactivate'}} Service</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="6">No Service meets your requirements</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            service: {},
        }
    },
    emits:['refreshServiceList'],
    methods:{
        addService(){
            this.loading = true;
            this.editMode = false;
            this.service = {};
            $('#serviceFormModal').modal('show');
            this.loading = false; 
        },
        closeModals(){
            $('#serviceFormModal').modal('hide');
        },
        deactivateService(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Service will no longer be available to people who visit your page",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, deactivate it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/consultant_practices/services/'+id)
                    .then(response=>{
                        this.$swal.fire('Deactivated!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        refreshPage(){
            this.closeModals();
            this.$emit('refreshServiceList');
        },
        startService(service){
            this.loading = true;
            this.editMode = false;
            this.dispute = dispute;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        updateService(service){
            this.loading = true;
            this.editMode = true;
            this.service = service;
            $('#serviceFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        services: Array,
        source: String,
    },
    watch:{}
}
</script>