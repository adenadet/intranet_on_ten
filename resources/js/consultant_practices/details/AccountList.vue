<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="accountFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{ editMode ? 'Edit' : 'New'}} Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <CPFormAccount :editMode="editMode" :company.sync="company" :account.sync="account" @refreshAccountForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed table-striped text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th v-if="source == 'admin' || source == 'finance'">Company </th>
                <th>Bank Name</th>
                <th>Account Name</th>
                <th>Account Number</th>
                <th>Status</th>
                <th><button v-if="source == 'admin' || source == 'finance'" class="btn btn-xs btn-primary float-right" @click="addAccount()"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="accounts.length > 0">
            <tr v-for="(account, index) in accounts" :key="account.id">
                <td>{{ addOne(index) }}</td>
                <td v-if="source == 'admin' || source == 'finance'">{{ company?.name }}</td>
                <td>{{ account.bank?.bank_name }}</td>
                <td>{{ account.account_name }}</td>
                <td>{{ account.account_number }}</td>
                <td>
                    <span v-if="account.status == 1" class="badge badge-primary">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <span class="nav-link float-right" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link class="btn btn-block dropdown-item" :to="'/consultant_practices/'+source+'/accounts/' + account.id"><i class="fas fa-eye mr-2 text-success"></i> View Account</router-link>
                        <button class="btn btn-block dropdown-item" @click="updateAccount(account)"><i class="fas fa-edit mr-2 text-primary"></i> Update Account</button>
                        <button class="btn btn-block dropdown-item" @click="deactivateAccount(account.id)"><i class="fas fa-power-off mr-2 text-danger"></i> {{ account.status == 1 ? 'Deactivate' : 'Reactivate' }} Account</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="7">No Account meets your requirements</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            account: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['refreshAccountList'],
    methods:{
        addAccount(){
            this.loading = true;
            this.editMode = false;
            this.account = {};
            $('#accountFormModal').modal('show');
            this.loading = false; 
        },
        closeModals(){
            $('#accountFormModal').modal('hide');
        },
        deactivateAccount(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Account will no longer be available",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, deactivate it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/consultant_practices/accounts/'+id)
                    .then(response=>{
                        this.$swal.fire('Deactivated!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        refreshPage(){
            this.closeModals();
            this.$emit('refreshAccountList');
        },
        updateAccount(account){
            this.loading = true;
            this.editMode = true;
            this.account = account;
            $('#accountFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        accounts: Array,
        company: {type:Object, default: null},
        source: String,
    },
}
</script>