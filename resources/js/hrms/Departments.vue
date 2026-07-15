<template>
<section class="card">
    <div class="card-header">
        <h3 class="card-title">Departments</h3>
    </div>
    <div class="card-body table-responsive p-0" style="height:500px">
        <HrmsDetailDepartmentList :departments.sync="departments.data" />
    </div>
    <div class="card-footer">
        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="departments.per_page != null ? departments.per_page : 52" :records="departments.total != null ? departments.total : 550" ></pagination>
    </div>
</section> 
</template>
<script>
export default {
    data(){
        return {
            current_page: 1,
            departments: {data:[], total: 0},
            loading: false,
            users: [],
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/ums/departments').then(response =>{
                this.departments = response.data.departments;
                this.users = response.data.users;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Departments were loaded successfully',
                });
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Departments were not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        getDepartments(page=1){
            axios.get('/api/ums/departments?page='+page)
            .then(response=>{
                this.departments = response.data.departments;   
            });
        },
        refresh(response){
            this.departments = response.data.departments;
            this.users = response.data.users;
        },
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>