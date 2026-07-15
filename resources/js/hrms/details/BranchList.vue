<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
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
        <tbody v-if="branches.length > 0">
            <tr v-for="branch in branches" :key="branch.id">
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
        <tbody v-else>
            <tr>
                <td colspan="7">No Branch meets your requirements</td>
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
        addBranch(){

        },
        deleteBranch(branch){},
        editBranch(branch){},
    },
    mounted() {
        //this.getAllInitials()
    },
    props:{
        branches:{type: Array, default: []},
        source: {type: String, default: 'hr'},
    }
}
</script>