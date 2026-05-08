<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="companyFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{ editMode ? 'Edit' : 'New'}} Company</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <CPFormCompany :editMode="editMode" :company.sync="company" @refreshCompany="refreshCompanyForm"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
            <h3 class="profile-username text-center">{{ company.name }}</h3>

            <div class="text-muted text-center" v-html="company.address"></div>

            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item"><b>Email</b> <a class="float-right">{{ company.email }}</a></li>
                <li class="list-group-item"><b>Phone</b> <a class="float-right">{{ company.phone }}</a></li>
                <li class="list-group-item"><b>Consultants</b> <a class="float-right">{{ company.consultants?.length || 0 }}</a></li>
                <li class="list-group-item"><b>Accounts</b> <a class="float-right">{{ company.accounts?.length || 0 }}</a></li>
                <li class="list-group-item"><b>Status</b> <a class="float-right">{{ company.status == 1 ? 'Active' : 'Inactive' }}</a></li>
            </ul>
            <button class="btn btn-primary btn-block" @click="updateCompany()"><b>Update Company</b></button>
            <button class="btn btn-danger btn-block" @click="deactivateCompany()" v-if="company.status == 1"><b>Deactivate Company</b></button>
            <button class="btn btn-success btn-block" @click="reactivateCompany()" v-else><b>Reactivate Company</b></button>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['refreshCompanyList'],
    methods:{
        addCompany(){
            this.loading = true;
            this.editMode = false;
            this.company = {};
            $('#companyFormModal').modal('show');
            this.loading = false; 
        },
        deactivateCompany(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Company and its Consultants will no longer be available",
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
                    this.form.delete('/api/consultant_practices/companies/'+this.company.id)
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
        reactivateCompany(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Company and its Consultants will be available",
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
                    this.form.delete('/api/consultant_practices/companies/'+this.company.id)
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
            this.$emit('refreshCompanyList');
        },
        updateCompany(){
            this.loading = true;
            this.editMode = true;
            $('#companyFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        company: Object,
        source: String,
    },
    watch:{}
}
</script>