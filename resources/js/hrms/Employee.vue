<template>
<section class="overlay-wrapper p-0">
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
                                    <button class="btn btn-primary btn-block"><b>Assign Managers</b></button>
                                </div>
                                <div class="card-body box-profile" v-else>
                                    <div class="card-body box-profile">
                                        <div class="text-center"><img class="profile-user-img img-fluid img-circle" :src="'/dist/img/user4-128x128.jpg'" alt="User profile picture"></div>
                                        <h3 class="profile-username text-center">Loading Details...</h3>
                                        <p class="text-muted text-center">Loading Department...</p>
                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item">
                                                <b>Followers</b> <a class="float-right">1,322</a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Following</b> <a class="float-right">543</a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Friends</b> <a class="float-right">13,287</a>
                                            </li>
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
                                <li class="nav-item"><a class="nav-link text-white" href="#settings" data-toggle="tab">Settings</a></li>
                                </ul>
                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content p-0">
                                    <div class="active tab-pane p-0" id="activity">
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
                                    <div class="tab-pane" id="settings">
                                        <div class="card p-0">
                                            <div class="card-header bg-navy">
                                                <h3 class="card-title">Other Information</h3> 
                                            </div>
                                            <div class="card-body">
                                                <form class="form-horizontal">
                                                    <div class="form-group row">
                                                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                                        <div class="col-sm-10">
                                                        <input type="email" class="form-control" id="inputName" placeholder="Name">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                                        <div class="col-sm-10">
                                                        <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2" class="col-sm-2 col-form-label">Name</label>
                                                        <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputName2" placeholder="Name">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputExperience" class="col-sm-2 col-form-label">Experience</label>
                                                        <div class="col-sm-10">
                                                        <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputSkills" class="col-sm-2 col-form-label">Skills</label>
                                                        <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                        <div class="checkbox">
                                                            <label>
                                                            <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                                                            </label>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                        <button type="submit" class="btn btn-danger">Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
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
        getAllInitials(){
            this.loading = true;
            axios.get('/api/ums/staffs/'+this.$route.params.id).then(response =>{
                this.areas = response.data.areas;
                this.branches = response.data.branches;
                this.departments = response.data.departments;
                this.employee = response.data.employee;
                this.states = response.data.states;
                this.users = response.data.users;
                this.loading = false;
                this.user = response.data.user;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Users not loaded successfully',
                })
            });
        },
        getUser(page=1){
            axios.get('/api/ums/users?page='+page)
            .then(response=>{
                this.users = response.data.users;   
            });
        },
        setUserRole(user){
            this.$Progress.start();
            this.user = user;
            Fire.$emit('userRoleUpdate', user);
            $('#roleModal').modal('show');
            this.$Progress.finish();
        },
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>