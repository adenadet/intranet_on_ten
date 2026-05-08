<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="consultantServiceFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{ editMode ? 'Edit' : 'New'}} Consultant Service</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <CPFormConsultantService :editMode="editMode" :consultant.sync="source == 'consultant' ? consultant : null" :consultant_service.sync="consultant_service" @refreshConsultantServiceForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed table-striped text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th v-if="source != 'consultant'">Consultant</th>
                <th>Service</th>
                <th>Price</th>
                <th>Status</th>
                <th><button class="btn btn-xs btn-primary float-right" @click="addConsultantService()"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="consultant_services.length > 0">
            <tr v-for="(consultant_service, index) in consultant_services" :key="consultant_service.id">

                <td>{{ addOne(index) }}</td>
                <td v-if="source != 'consultant'">{{ consultant_service.consultant?.title }}. {{ consultant_service.consultant?.first_name }} {{ consultant_service.consultant?.last_name }}</td>
                <td>{{ consultant_service.service?.name || 'No Service Assigned' }}</td>
                <td>{{ currency(consultant_service.price) }}</td>
                <td>
                    <span v-if="consultant_service.status == 1" class="badge badge-primary">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <span class="nav-link float-right" data-toggle="dropdown" href="#"><i class="fa fa-ellipsis-v"></i></span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link class="btn btn-block dropdown-item" :to="'/consultant_service_practices/admin/consultant_services/' + consultant_service.id"><i class="fas fa-eye mr-2 text-success"></i> View ConsultantService</router-link>
                        <button class="btn btn-block dropdown-item" @click="updateConsultantService(consultant_service)"><i class="fas fa-edit mr-2 text-primary"></i> Update ConsultantService</button>
                        <button class="btn btn-block dropdown-item" @click="deactivateConsultantService(consultant_service.id)"><i class="fas fa-power-off mr-2 text-danger"></i> {{ consultant_service.status == 1 ? 'Deactivate' : 'Reactivate' }} ConsultantService</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="6">No Consultant Service meets your requirements</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            consultant_service: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['refreshConsultantServiceList'],
    methods:{
        addConsultantService(){
            this.loading = true;
            this.editMode = false;
            this.consultant_service = {services:[],};
            $('#consultantServiceFormModal').modal('show');
            this.loading = false; 
        },
        closeModals(){
            $('#consultantServiceFormModal').modal('hide');
        },
        deactivateConsultantService(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This ConsultantService will no longer be available",
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
                    this.form.delete('/api/consultant_service_practices/consultant_services/'+id)
                    .then(response=>{
                        this.$swal.fire('Deactivated!', response.data.message, 'success');
                        this.refreshPage();
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
            this.$emit('refreshConsultantServiceList');
        },
        updateConsultantService(consultant_service){
            this.loading = true;
            this.editMode = true;
            this.consultant_service = consultant_service;
            $('#consultantServiceFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        consultant: Object,
        consultant_services: Array,
        source: String,
    },
    watch:{}
}
</script>