<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="specialtyFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Specialty Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <CPFormSpecialty :editMode.sync="editMode" :specialty.sync="specialty" @refreshSpecialtyForm="refreshPage" />
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
                <th>Description</th>
                <th>Status</th>
                <th><button class="btn btn-sm btn-primary float-right" @click="addSpecialty"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="specialties.length > 0">
            <tr v-for="(specialty,index) in specialties">
                <td>{{ addOne(index) }}</td>
                <td>{{ specialty.name }}</td>
                <td :title="specialty.description" v-html="readMore(specialty.description, 50, '...')"></td>
                <td><span v-if="specialty.status === 1" class="badge badge-success">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="btn btn-block dropdown-item" @click="viewSpecialty(specialty)"><i class="fas fa-eye mr-2 text-success"></i> View Specialty</button>
                        <button class="btn btn-block dropdown-item" @click="updateSpecialty(specialty)"><i class="fas fa-edit mr-2 text-primary"></i> Update Specialty</button>
                        <button class="btn btn-block dropdown-item" @click="deactivateSpecialty(specialty.id)"><i class="fas fa-times mr-2 text-danger"></i> {{specialty.status == 1 ? 'Deactivate' : 'Reactivate'}} Specialty</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="6">No Specialty meets your requirements</td>
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
            specialty: {},
        }
    },
    emits:['refreshSpecialtyList'],
    methods:{
        addSpecialty(){
            this.loading = true;
            this.editMode = false;
            this.specialty = {};
            $('#specialtyFormModal').modal('show');
            this.loading = false; 
        },
        closeModals(){
            $('#specialtyFormModal').modal('hide');
        },
        deactivateSpecialty(id){
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
            this.$emit('refreshSpecialtyList');
        },
        startSpecialty(specialty){
            this.loading = true;
            this.editMode = false;
            this.dispute = dispute;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        updateSpecialty(specialty){
            this.loading = true;
            this.editMode = true;
            this.specialty = specialty;
            $('#specialtyFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        specialties: Array,
        source: String,
    },
    watch:{}
}
</script>