<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="patientFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Patient Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <CPFormPatient :editMode.sync="editMode" :patient.sync="patient" @refreshPatientForm="refreshPage" />
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
                <th>Unique ID</th>
                <th>Patient Type</th>
                <th>Gender</th>
                <th>Status</th>
                <th><button class="btn btn-sm btn-primary float-right" @click="addPatient"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="patients.length > 0">
            <tr v-for="(patient, index) in patients">
                <td>{{ addOne(index) }}</td>
                <td>{{ patient.name }}</td>
                <td>{{ patient.unique_id }}</td>
                <td>
                    <span v-if="patient.patient_type == 'hmo'">Credit</span>
                    <span v-else>Cash</span>
                </td>
                <td><span v-html="patient.sex == 'male' ? 'Male' : 'Female'"></span></td>
                <td><span :class="{'badge badge-success': patient.status == 1, 'badge badge-danger': patient.status == 0}">{{ patient.status == 1 ? 'Active' : 'Inactive' }}</span></td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#"><i class="fa fa-ellipsis-v"></i></span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" >
                        <router-link v-if="source == 'admin'" class="btn btn-block dropdown-item" :to="'/consultant_practices/admin/patients/' + patient.id"><i class="fas fa-eye mr-2 text-success"></i> View Patient</router-link>
                        <router-link v-else-if="source == 'finance'" class="btn btn-block dropdown-item" :to="'/consultant_practices/finance/patients/' + patient.id"><i class="fas fa-eye mr-2 text-success"></i> View Patient</router-link>
                        <router-link v-else-if="source == 'front'" class="btn btn-block dropdown-item" :to="'/consultant_practices/front/patients/' + patient.id"><i class="fas fa-eye mr-2 text-success"></i> View Patient</router-link>
                        <router-link v-else-if="source == 'medical'" class="btn btn-block dropdown-item" :to="'/consultant_practices/medical/patients/' + patient.id"><i class="fas fa-eye mr-2 text-success"></i> View Patient</router-link>
                        <button class="btn btn-block dropdown-item" @click="updatePatient(patient)"><i class="fas fa-inbox mr-2 text-primary"></i> Update Patient</button>
                        <button class="btn btn-block dropdown-item" @click="deactivatePatient(patient.id)"><i class="fas fa-times mr-2 text-danger"></i> {{patient.status == 1 ? 'Deactivate' : 'Reactivate'}} Patient</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="7">No Patient meets your requirements</td></tr>
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
            patient: {},
        }
    },
    emits:['refreshPatientList'],
    methods:{
        addPatient(){
            this.loading = true;
            this.editMode = false;
            this.patient = {};
            $('#patientFormModal').modal('show');
            this.loading = false; 
        },
        deactivatePatient(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Session will no longer be available to people who visit your page",
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
                    this.form.delete('/api/consultant_practices/sessions/'+id)
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
            this.$emit('refreshPatientList');
        },
        startPatient(patient){
            this.loading = true;
            this.editMode = false;
            this.dispute = dispute;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        updatePatient(patient){
            this.loading = true;
            this.editMode = true;
            this.patient = patient;
            $('#patientFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        patients: Array,
        source: String,
    },
    watch:{}
}
</script>