<template>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-widget widget-user">
                <div class="widget-user-header bg-info">
                    <h3 class="widget-user-username">{{department.hod != null ? department.hod.first_name+' '+department.hod.last_name : 'Departmental Head'}}</h3>
                    <h5 class="widget-user-desc">Head of Department</h5>
                </div>
                <div class="widget-user-image"><img :src="((department.hod != null) && (department.hod.image)) ? '/img/profile/'+department.hod.image : '/img/profile/default.png'" width="300" height="auto" alt="avatar" :title="department.hod != null ? department.hod.first_name+' '+department.hod.last_name : 'Departmental Head'"></div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">{{department.users != null && typeof(department.users) != 'undefined'? department.users.length: 0}}</h5>
                                <span class="description-text"><i class="fa fa-users"></i></span>
                            </div>
                        </div>
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">{{department.email}}</h5>
                                <span class="description-text"><i class="fa fa-envelope"></i></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="description-block">
                                <h5 class="description-header">{{department.ext_phone}}</h5>
                                <span class="description-text"><i class="fa fa-phone"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 d-flex align-items-stretch" v-for="user in users" :key="user.id">
                            <ContactSingle :user="user" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            department: {},
            dept_users: [],
            form: new Form({}),
            loading: false,
            users: [],
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/ums/departments/'+this.$route.params.id).then(response =>{
                this.department = response.data.department;
                this.users = response.data.department.users;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Department were loaded successfully',
                });
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Department were not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>