<template>
<div class="container-fluid">
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Branchs</h3>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table m-b-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Practice Manager</th>
                                    <th>Consultant in Charge</th>
                                    <th>Head of Nurse </th>
                                    <th>Number</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="branch in branches.data" :key="branch.id">
                                    <td>{{branch.name}}</td>
                                    <td>{{branch.pm_id !== null ? branch.practice_manager.first_name+' '+branch.practice_manager.last_name : ''}}</td>
                                    <td>{{branch.cinc_id !== null ? branch.chief_consultant.first_name+' '+branch.chief_consultant.last_name : ''}}</td>
                                    <td>{{branch.hon_id !== null ? branch.head_nurse.first_name+' '+branch.head_nurse.last_name : ''}}</td>
                                    <td>{{branch.users.length}}</td>
                                    <td :title="branch.address">{{branch.address}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <router-link :to="'/branches/'+branch.id" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></router-link>
                                        </div>          
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="branches.per_page != null ? branches.per_page : 52" :records="branches.total != null ? branches.total : 550" ></pagination>
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
            branches: {},
        }
    },
    methods:{
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/ums/branches').then(response =>{
                this.branches = response.data.branches;
                this.users = response.data.users;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Branches were loaded successfully',
                });
            })
            .catch(()=>{
                toast.fire({
                    icon: 'error',
                    title: 'Branches were not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        refresh(response){
            this.branches = response.data.branches;
            this.users = response.data.users;
        },
    },
    mounted() {
        this.getAllInitials()
    }
}
</script>