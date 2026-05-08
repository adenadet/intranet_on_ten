<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="consultantFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{ editMode ? 'Edit' : 'New'}} Consultant</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <CPFormConsultant :editMode="editMode" :consultant.sync="consultant" @refreshConsultantForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed table-striped text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Specialty</th>
                <th v-if="source != 'company'">Company</th>
                <th>Status</th>
                <th><button class="btn btn-xs btn-primary float-right" @click="addConsultant()"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="consultants.length > 0">
            <tr v-for="(consultant, index) in consultants" :key="consultant.id">

                <td>{{ addOne(index) }}</td>
                <td>{{ consultant.title }}. {{ consultant.first_name }} {{ consultant.last_name }}</td>
                <td>{{ consultant.specialty?.name || 'No Specialty Assigned' }}</td>
                <td v-if="source != 'company'">{{ consultant.company?.name }}</td>
                <td>
                    <span v-if="consultant.status == 1" class="badge badge-primary">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <span class="nav-link float-right" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link class="btn btn-block dropdown-item" :to="'/consultant_practices/admin/consultants/' + consultant.id"><i class="fas fa-eye mr-2 text-success"></i> View Consultant</router-link>
                        <button class="btn btn-block dropdown-item" @click="updateConsultant(consultant)"><i class="fas fa-edit mr-2 text-primary"></i> Update Consultant</button>
                        <button class="btn btn-block dropdown-item" @click="deactivateConsultant(consultant.id)"><i class="fas fa-power-off mr-2 text-danger"></i> {{ consultant.status == 1 ? 'Deactivate' : 'Reactivate' }} Consultant</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="6">No Consultant meets your requirements</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            consultant: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['refreshConsultantList'],
    methods:{
        addConsultant(){
            this.loading = true;
            this.editMode = false;
            this.consultant = {services:[],};
            $('#consultantFormModal').modal('show');
            this.loading = false; 
        },
        closeModals(){
            $('#consultantFormModal').modal('hide');
        },
        deactivateConsultant(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Consultant will no longer be available",
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
                    this.form.delete('/api/consultant_practices/consultants/'+id)
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
            this.$emit('refreshConsultantList');
        },
        updateConsultant(consultant){
            this.loading = true;
            this.editMode = true;
            this.consultant = consultant;
            $('#consultantFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        consultants: Array,
        source: String,
    },
    watch:{}
}
</script>