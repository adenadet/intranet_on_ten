<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-striped table-hover m-b-0">
        <thead>
            <tr>
                <th>Name</th>
                <th>HOD</th>
                <th>Email</th>
                <th>Phone Ext.</th>
                <th>Number</th>
                <th>Description</th>
                <th><button class="btn btn-primary btn-xs" @click="addDepartment" type="button"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="departments.length > 0">
            <tr v-for="department in departments" :key="department.id">
                <td>{{department.name}}</td>
                <td>{{department.hod_id !== null ? department.hod.first_name+' '+department.hod.last_name : ''}}</td>
                <td>{{department.email}}</td>
                <td>{{department.ext}}</td>
                <td>{{department.users?.length || 0}}</td>
                <td :title="department.description">{{ readMore(department.description, 25, '...')}}</td>
                <td>
                    <div class="btn-group">
                        <router-link :to="'/hrms/admin/departments/'+department.id" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></router-link>
                    </div>          
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="7">No department meets your requirements</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            branch: {},
            loading: false,
        }
    },
    methods:{
        addDepartment(){

        },
        deleteDepartment(department){},
        editDepartment(department){},
    },
    mounted() {
        //this.getAllInitials()
    },
    props:{
        departments:{type: Array, default: []},
        source: {type: String, default: 'hr'},
    }
}
</script>