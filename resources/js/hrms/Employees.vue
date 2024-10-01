<template>
<section class="overlay-wrapper contain-fluid">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-12">
            <div class="card p-0">
                <div class="card-header bg-navy">All Employees</div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap">
                        <thead class="bg-dark">
                            <tr>
                                <th>Staff ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Supervisor</th>
                                <th>Line Manager</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="employee in employees.data" :key="employee.id">
                                <td>SNH-{{ employee.employee_id }}</td>
                                <td>{{ FullName(employee.user) }}</td>
                                <td>{{ employee.department != null ? employee.department.name : "No Department" }}</td>
                                <td>{{ employee.designation != null ? employee.designation.name : "No Designation" }}</td>
                                <td>{{ employee.supervisor != null ? FullName(employee.supervisor.user) : 'No Supervisor Assigned Yet' }}</td>
                                <td>{{ employee.line_manager != null ? FullName(employee.line_manager.user) : 'No Supervisor Assigned Yet'  }}</td>
                                <td>{{ employee.employment_status != null ? 'Active' : 'Undefined' }}</td>
                                <td>{{ ExcelDate(employee.date_of_joining) }} {{(employee.date_of_leaving != null && employee.date_of_joining != '') ? ' - '+ExcelDate(employee.date_of_leaving) : ' Till Now'  }}</td>
                                <td>
                                    <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                        <router-link :to="'/hrms/admin/employees/'+employee.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Employee</button></router-link>
                                        <button class="dropdown-item btn btn-block btn-sm" @click="assignSupervisor(employee)"><i class="fa fa-user-tag mr-1 text-success"></i> Assign Supervisor/LM</button>
                                        <button class="dropdown-item btn btn-block btn-sm" @click="modifyEmployee(employee)"><i class="fa fa-edit mr-1 text-success"></i> Update Record</button>
                                        <button class="dropdown-item btn btn-block btn-sm" @click="deactivateEmployee(employee)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Employee</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-navy">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="employees.per_page != null ? employees.per_page : 52" :records="employees.total != null ? employees.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import Form from 'vform';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

export default {
    data(){
        return {
            current_page: 1,
            areas:[],
            branches:[],
            departments:[],
            editMode: false,
            employees: {},
            form: new Form({}),
            loading: false,
            query: '',
            savings:{},
            states:[],
            employee:{},
            users:{},
        }
    },
    methods:{
        addUser(){
            this.editMode = false;
            this.user = {};
            $('#userModal').modal('show');
        },
        closeModals(){
            $('#userModal').modal('hide'); 
            $('#roleModal').modal('hide');
            //this.users = response.data.users;
        },
        deleteUser(id){
            Swal.fire({
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
                    this.form.delete('/api/ums/staffs/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        editUser(user){
            this.editMode = true;
            this.user = user;
            $('#userModal').modal('show');
        },
        getAllInitials(page=1){
            this.loading = true
            axios.get('/api/ums/staffs?page='+page).then(response =>{
                this.refreshPage(response);
                this.loading = false;
                toast.fire({icon: 'success', title: 'Users loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({icon: 'error', title: 'Users not loaded successfully',})
            });
        },
        refreshPage(response){
            this.areas = response.data.areas;
            this.branches = response.data.branches;
            this.current_page = response.data.users.current_page;
            this.departments = response.data.departments;
            this.employees = response.data.employees;
            this.states = response.data.states;
            this.users = response.data.users;
            this.closeModals();
        },
        searchUser(){
            //let query = this.$parent.search;
            axios.get('/api/ums/users/search?q='+query)
            .then((response ) => {this.users = response.data.users;})
            .catch(()=>{});
        },
        setUserRole(user){
            this.user = user;
            this.editMode = true;
            $('#roleModal').modal('show');
        },
    },
    mounted(){ 
        this.getAllInitials();
    },
}
</script>