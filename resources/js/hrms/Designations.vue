<template>
    <section>
        <div class="modal fade" id="designationModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Designation Detail</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <HrmsDetailDesignation :designation.sync="designation" @reload="refreshPage"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="designationFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">{{editMode ? 'Update ': 'Create New '}} Designation</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <HrmsFormDesignation :designation.sync="designation" :editMode.sync="editMode" @reload="refreshPage"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-navy">
                <h3 class="card-title">Designations</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default btn-xs" @click="searchDesignation"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body overlay-wrapper table-responsive p-0" style="height: 600px;">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <table class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(designation, index) in designations.data">
                            <td>{{ addOne(index) }}</td>
                            <td>{{ designation.name }}</td>
                            <td>{{ designation.department != null ? designation.department.name : 'N/A' }}</td>
                            <td>{{ designation.unit != null ? designation.unit.name : 'N/A' }}</td>
                            <td>{{ designation.employees != null ? designation.employees.length : 0 }}</td>
                            <td :title="designation.description">{{ readMore(designation.description, 25, '...') }}</td>
                            <td>
                                <button class="nav-link btn btn-xs btn-default" data-toggle="dropdown" type="button">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <button class="dropdown-item btn btn-block btn-sm" @click="viewDesignation(designation)"><i class="fa fa-eye mr-1 text-primary"></i> View Designation</button>
                                    <button class="dropdown-item btn btn-block btn-sm" @click="editDesgination(designation)"><i class="fa fa-edit mr-1 text-warning"></i> Edit Designation</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="designations.per_page != null ? designations.per_page : 52" :records="designations.total != null ? designations.total : 550" ></pagination>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data(){
        return {
            current_page: 1,
            designation: {},
            designations: {},
            loading: false,
            query: '',
        }
    },
    methods:{
        addDesignation(){
            this.editMode = false;
            this.designation = {};
            $('#designationFormModal').modal('show');
        },
        closeModals(){
            $('#userModal').modal('hide'); 
            $('#roleModal').modal('hide');
            //this.users = response.data.users;
        },
        deleteDesignation(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/hrms/designations/'+id)
                    .then(response=>{
                        this. $swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        editDesignation(user){
            this.editMode = true;
            this.user = user;
            $('#userModal').modal('show');
        },
        getAllInitials(page=1){
            this.loading = true
            axios.get('/api/hrms/designations?page='+page).then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({icon: 'success', title: 'Designations loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Designations not loaded successfully',})
            });
        },
        refreshPage(response){
            this.designations = response.data.designations;
            this.closeModals();
        },
        searchDesignation(){
            axios.get('/api/hrms/designations/search/'+this.query)
            .then((response ) => {this.designations = response.data.designations;})
            .catch(()=>{});
        },
        viewDesignation(designation){
            this.designation = designation;
            $('#designationModal').modal('show');
        },
    },
    mounted(){ 
        this.getAllInitials();
    },
}
</script>