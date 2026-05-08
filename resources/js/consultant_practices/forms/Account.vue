<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Company Name</label>
                    <div class="form-control" id="bank_id" v-if="company != null">
                        {{company.name}}
                        <input type="hidden" v-model="accountData.company_id" />
                    </div>
                    <select class="form-control" id="bank_id" v-model="accountData.company_id" v-else>
                        <option value="">--Select Company--</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Bank Name</label>
                    <select class="form-control" id="bank_id" v-model="accountData.bank_id">
                        <option value="">--Select Bank--</option>
                        <option v-for="bank in banks" :key="bank.id" :value="bank.id">{{ bank.bank_name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label>Account Name</label>
                    <input type="text" class="form-control" id="account_name" v-model="accountData.account_name" placeholder="Enter account name">
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label>Account Number</label>
                    <input type="text" class="form-control" id="account_number" v-model="accountData.account_number" placeholder="Enter account number">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="accountData.status">
                        <option value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary" @click.prevent="editMode ? updateAccount():createAccount()">{{editMode ? 'Update' : 'Create'}} Account</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            accountData: new Form({
                id: '',
                account_name: '',
                account_number: '',
                bank_id: '',
                company_id: '',
                is_primary: '',
                status: '',
            }),
            companies: [],
            loading: false,
        }
    },
    emits:['refreshAccount'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        createAccount(){
            this.loading = true;
            this.accountData.company_id = this.company.id;
            this.accountData.post('/api/consultant_practices/accounts')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'A new account was created successfully',
                });
                this.$emit('refreshAccount', response);
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        getAllInitials(){
            this.loading= true;
            axios.get('/api/consultant_practices/accounts/initials')
            .then(response =>{
                this.banks = response.data.banks;
                this.companies = response.data.companies;
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Account form not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        updateAccount(){
            this.loading = true;
            this.accountData.company_id = this.company.id;
            this.accountData.put('/api/consultant_practices/accounts/'+this.accountData.id)
            .then(response =>{
                this.$emit('refreshAccount', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Account details have been modified',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            })
            .finally(()=>{
                this.loading = false;
            });
        },
    },
    props:{
        editMode: Boolean,
        account: {Object, default:null},
        company: {type: Object, default: null},
    },
    watch:{
        account(){
            this.accountData.fill(this.account);
        }
    },
    watch:{
        company(){
            if (this.company != null && this.company.id != null) {
                this.accountData.company_id = this.company.id;
            }
        }
    }
}
</script>