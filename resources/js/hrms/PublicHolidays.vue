<template>
<section>
    <div class="row">
        <div class="modal fade" id="publicHolidayFormModal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Public Holiday Details</h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body p-0">
                        <HrmsFormPublicHoliday :editMode.sync="editMode" :public_holiday.sync="public_holiday" @refreshPage="getAllInitials"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-navy">
                    <h3 class="card-title">Public Holiday</h3>
                    <div class="card-tools">
                        <div class="input-group" style="width: 500px;">
                            <select name="status" class="form-control" placeholder="Search" v-model="status">
                                <option value="">--Select Status</option>
                                <option value=1>Active</option>
                                <option value=0>Inactive</option>
                            </select>
                            <div class="input-group-append">
                                <input type="date" class="form-control ml-1" v-model="start_date" />
                                <input type="date" class="form-control ml-1" v-model="end_date" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 600px;">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <table class="table table-hover table-head-fixed table-striped text-nowrap">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Updated By</th>
                                <th><button class="btn btn-xs btn-primary" @click="addPublicHoliday"><i class="fa fa-plus"></i></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(public_holiday, index) in public_holidays.data" :key="public_holiday.id">
                                <td>{{ addOne(index) }}</td>
                                <td>{{ public_holiday.date }}</td>
                                <td>
                                    <span v-if="public_holiday.status == 1" class="badge badge-success">Active</span>
                                    <span v-else class="badge badge-danger">Inactive</span>
                                </td>
                                <td>{{ FullName(public_holiday.creator) }}</td>
                                <td>{{ FullName(public_holiday.updater) }}</td>
                                <td><button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                        <button class="dropdown-item btn btn-block btn-sm" @click="updatePublicHoliday(public_holiday)"><i class="fa fa-edit mr-1 text-primary"></i> Update Public Holiday</button>
                                        <button class="dropdown-item btn btn-block btn-sm" @click="deletePublicHoliday(public_holiday.id)"><i class="fa fa-user mr-1 text-danger"></i> Deactivate Public Holiday</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-navy">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="public_holidays.per_page != null ? public_holidays.per_page : 52" :records="public_holidays.total != null ? public_holidays.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            current_page: 1,
            end_date: '',
            editMode: false,
            form: new Form({}),
            loading: false,
            public_holidays: {data: [], total: 0, per_page: 0, current_page: 1, last_page: 0},
            public_holiday: {},
            query: '',
            start_date: '',
            status: 1,
        }
    },
    methods:{
        addPublicHoliday(){
            this.loading = true;
            this.public_holiday = {};
            this.editMode = false;
            $('#publicHolidayFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#publicHolidayFormModal').modal('hide'); 
        },
        deletePublicHoliday(id){
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
                    this.form.delete('/api/hrms/public_holidays/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Public Holiday Deleted', 'success');
                        this.getAllInitials();
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    })
                    .finally(()=>{
                        this.loading = false;
                    });
                }
            });  
        },
        getAllInitials(){
            this.loading = true
            axios.get('/api/hrms/public_holidays?start_date='+this.start_date+'&end_date='+this.end_date+'&page='+this.current_page+'&status='+this.status).then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Employees not loaded successfully',})
            });
        },
        refreshPage(response){
            this.departments = response.data.departments;
            this.public_holidays = response.data.public_holidays;
            this.closeModals();
        },
        searchEmployee(){
            axios.get('/api/hrms/employees/search/'+this.query)
            .then((response ) => {this.refreshPage(response);})
            .catch(()=>{});
        },
        updatePublicHoliday(public_holiday){
            this.loading = true;
            this.public_holiday = public_holiday;
            this.editMode = true;
            $('#publicHolidayFormModal').modal('show');
            this.loading = false;
        },
    },
    mounted(){ 
        this.getAllInitials();
    },
}
</script>