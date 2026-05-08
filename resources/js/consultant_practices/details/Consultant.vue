<template>
<section class="overlay-wrapper p-0">
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
    <div class="card card-primary card-outline">
        <div class="card-body box-profile" v-if="consultant != null">
            <h3 class="profile-username text-center">{{ consultant.title }}. {{ consultant.first_name }} {{ consultant.last_name }}</h3>

            <p class="text-muted text-center">{{ consultant.company?.name }}</p>

            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item"><b>Specialty</b> <a class="float-right">{{ consultant.specialty?.name }}</a></li>
                <li class="list-group-item"><b>Phone</b> <a class="float-right">{{ consultant.phone }}</a></li>
                <li class="list-group-item"><b>Email</b> <a class="float-right">{{ consultant.email }}</a></li>
                <li class="list-group-item"><b>Sex</b> <a class="float-right">{{ firstUp(consultant.sex) }}</a></li>
                <li class="list-group-item"><b>Services</b> <a class="float-right">{{ (consultant.services?.length || 0) }}</a></li>
            </ul>

            <button @click="updateConsultant" class="btn btn-primary btn-block"><b>Update Consultant</b></button>
            <button v-if="consultant.status == 1" @click="deactivateConsultant(consultant.id)" class="btn btn-danger btn-block"><b>Deactivate Consultant</b></button>
            <button v-else @click="reactivateConsultant(consultant.id)" class="btn btn-success btn-block"><b>Reactivate Consultant</b></button>
        
        </div>
    </div>
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
        reactivateConsultant(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Consultant will be reactivated",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reactivate it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/consultant_practices/consultants/'+id)
                    .then(response=>{
                        this.$swal.fire('Reactivated!', response.data.message, 'success');
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
        updateConsultant(){
            this.loading = true;
            this.editMode = true;
            this.consultant = consultant;
            $('#consultantFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        consultant: Object,
        source: String,
    },
    watch:{}
}
</script>