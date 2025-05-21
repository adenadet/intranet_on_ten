<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="assignManagerModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Assign Leave Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white"><i class="fa fa-times text-white"></i></span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployeeAssignManager :employee.sync="employee" @refreshPage="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="leaveTypeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Assign Leave Type</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployeeLeaveType :editMode.sync="editMode" :employee.sync="employee" @refreshPage="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card p-0">
                <div class="card-header bg-navy">Employee Details</div>
                <div class="card-body overlay-wrapper p-0">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <div class="row p-3">
                        <div class="col-md-3">
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile" v-if="employee != null && employee.user != null">
                                    <div class="text-center">
                                        <img class="profile-user-img img-fluid img-circle" :src="(employee.user.image) ? '/img/profile/'+employee.user.image : '/img/profile/default.png'" alt="User profile picture">
                                    </div>
                                    <h3 class="profile-username text-center">{{ FullName(employee.user)}}</h3>
                                    <p class="text-muted text-center">{{employee.designation != null ? employee.designation.name : 'Staff'}}</p>
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Designation</b> <a class="float-right">{{ employee.designation != null ? employee.designation.name : 'No Designation Assigned'  }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Department</b> <a class="float-right">{{ employee.department != null ? employee.department.name : 'No Department Assigned'  }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Line Manager</b> <a class="float-right">{{ employee.line_manager != null ? FullName(employee.line_manager.user) : 'No Line Manager Assigned'}}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Supervisor</b> <a class="float-right">{{ employee.supervisor != null ? FullName(employee.supervisor.user) : 'No Supervisor Assigned'}}</a>
                                        </li>
                                    </ul>
                                    <div class="row">
                                        <button class="col-md-6 btn btn-primary" @click="assignManager()"><b>Assign Managers</b></button>
                                        <button class="col-md-6 btn btn-outline-primary" @click="assignLeaveTypes()"><b>Assign Leave Type</b></button>
                                    </div>
                                </div>
                                <div class="card-body box-profile" v-else>
                                    <div class="card-body box-profile">
                                        <div class="text-center"><img class="profile-user-img img-fluid img-circle" :src="'/dist/img/user4-128x128.jpg'" alt="User profile picture"></div>
                                        <h3 class="profile-username text-center">Loading Details...</h3>
                                        <p class="text-muted text-center">Loading Department...</p>
                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item"><b>Followers</b> <a class="float-right">1,322</a></li>
                                            <li class="list-group-item"><b>Following</b> <a class="float-right">543</a></li>
                                            <li class="list-group-item"><b>Friends</b> <a class="float-right">13,287</a></li>
                                        </ul>
                                        <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card">
                            <div class="card-header p-2 bg-dark">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active text-white" href="#activity" data-toggle="tab">Basic</a></li>
                                    <li class="nav-item"><a class="nav-link text-white" href="#timeline" data-toggle="tab">Employee</a></li>
                                    <li class="nav-item"><a class="nav-link text-white" href="#leaves" data-toggle="tab">Leaves</a></li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="activity">
                                        <div class="card p-0">
                                            <div class="card-header bg-navy">
                                                <h3 class="card-title">Basic Information</h3> 
                                            </div>
                                            <div class="card-body">
                                                <UserDetailBioData :user.sync="employee.user != null ? employee.user : {}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="timeline">
                                        <div class="card p-0">
                                            <div class="card-header bg-navy">
                                                <h3 class="card-title">Employee Information</h3> 
                                            </div>
                                            <div class="card-body">
                                                <HrmsDetailEmployee :employee.sync="employee" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="leaves">
                                        <HrmsDetailEmployeeLeaveType :leave_types.sync="employee.leave_types" source="admin"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            areas:[],
            branches:[],
            departments:[],
            editMode: false,
            employee: {
                user: {},
            },
            loading: false,
            savings:{},
            states:[],
            user:{},
            users:{},
        }
    },
    methods:{
        assignLeaveTypes(){
            $('#leaveTypeModal').modal('show');
        },
        assignManager(){
            $('#assignManagerModal').modal('show');
        },
        closeModal(response){
            $('#EmployeeModal').modal('hide');
            $('#assignManagerModal').modal('hide');
            $('#leaveTypeModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/employees/'+this.$route.params.id).then(response =>{
                //this.areas = response.data.areas;
                //this.branches = response.data.branches;
                //this.departments = response.data.departments;
                this.employee = response.data.employee;
                //this.states = response.data.states;
                //this.users = response.data.users;
                this.loading = false;
                //this.user = response.data.user;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Employee Detail was not loaded successfully',
                })
            });
        },
        refreshPage(){
            this.closeModal();
            this.getAllInitials();
        },
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>