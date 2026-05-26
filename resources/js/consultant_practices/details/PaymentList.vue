<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="paymentFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Payment Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <CPFormPayment :editMode.sync="editMode" :payment.sync="payment" @refreshPaymentForm="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap table-striped ">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Date</th>
                <th>Company</th>
                <th>Account</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Status</th>
                <th><button class="btn btn-sm btn-primary float-right" @click="addPayment"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="payments.length > 0">
            <tr v-for="payment in payments" key="payment.id">
                <td>{{ ExcelDate(payment.date) }}</td>
                <td>{{ payment.company?.name || 'General' }}</td>
                <td>{{ payment.account?.bank?.bank_name+' ['+payment.account?.account_name+'-'+payment.account?.account_number+']' }}</td>
                <td>{{ payment.account != null ? (payment.account.bank?.bank_new || 'New Bank')+' '+payment.account.account_name+''+payment.account.account_number : 'General' }}</td>
                <td>{{ currency(payment.amount) }}</td>
                <td :title="payment.description" v-html="readMore(payment.description, 50, '...')"></td>
                <td><span v-if="payment.status === 1" class="badge badge-dark">Pending</span>
                    <span v-else-if="payment.status === 10" class="badge badge-success">Confirmed</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link class="btn btn-block dropdown-item" :to="'/consultant_practices/'+type+'/payments/'+payment.id"><i class="fas fa-eye mr-2 text-success"></i> View Payment</router-link>
                        <button class="btn btn-block dropdown-item" @click="updatePayment(payment)"><i class="fas fa-edit mr-2 text-primary"></i> Update Payment</button>
                        <button v-if="payment.status == 10"class="btn btn-block dropdown-item" @click="reversePayment(payment.id)"><i class="fas fa-times mr-2 text-danger"></i>  Reverse Payment</button>
                        <button v-if="payment.status == 1"class="btn btn-block dropdown-item" @click="deactivatePayment(payment.id)"><i class="fas fa-times mr-2 text-danger"></i> {{payment.status == 1 ? 'Deactivate' : 'Reactivate'}} Payment</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="8">No Payment meets your requirements</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            payment: {},
        }
    },
    emits:['refreshPaymentList'],
    methods:{
        addPayment(){
            this.loading = true;
            this.editMode = false;
            this.payment = {};
            $('#paymentFormModal').modal('show');
            this.loading = false; 
        },
        closeModals(){
            $('#paymentFormModal').modal('hide');
        },
        deactivatePayment(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Payment will no longer be available to people who visit your page",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Carry On'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/consultant_practices/payments/'+id)
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
            this.$emit('refreshPaymentList');
        },
        reversePayment(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Payment will reversed and cancelled",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reverse it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.get('/api/consultant_practices/payments/'+id+'/reverse')
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
        updatePayment(payment){
            this.loading = true;
            this.editMode = true;
            this.payment = payment;
            $('#paymentFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        payments: {type: Array, default: () => []},
        source: {type: String, default:'',},
        type: {type: String, default: 'finance'},
    },
    watch:{}
}
</script>