<template>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="confirmFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{ editMode ? 'Edit' : 'New'}} Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <CPFormPaymentConfirmation :payment.sync="payment" @refreshPaymentConfirmationForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reversalFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Payment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <CPFormPaymentReversal :payment.sync="payment" @refreshPaymentReversalForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-primary card-outline">
        <div class="card-body box-profile overlay-wrapper">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <h3 class="profile-username text-center">{{ payment.company?.name }}</h3>
            <p class="text-muted text-center">{{ currency(payment.amount) }}</p>

            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item"><b>Bank</b> <a class="float-right">{{ payment.account?.bank?.bank_name || 'Unknown Bank' }}</a></li>
                <li class="list-group-item"><b>Account Name</b> <a class="float-right">{{ payment.account?.account_name || 'N/A'}}</a></li>
                <li class="list-group-item"><b>Number</b> <a class="float-right">{{ payment.account?.account_number || 'N/A'}}</a></li>
                <li class="list-group-item"><b>Status</b> 
                    <span v-if="payment.status === 1" class="badge bg-warning float-right">Pending</span>
                    <span v-else-if="payment.status === 10" class="badge badge-success float-right">Confirmed</span>
                    <span v-else-if="payment.status === 100" class="badge badge-dark float-right">Cancelled</span>
                    <span v-else-if="payment.status === 200" class="badge badge-danger float-right">Reversed</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </li>
                <li class="list-group-item"><b>Created By</b> <a class="float-right">{{ FullName(payment.creator)}}</a></li>
                <li v-if="payment.confirmer != null" class="list-group-item"><b>Confirmed By</b> <a class="float-right">{{ FullName(payment.confirmer)}}</a></li>
                <li v-if="payment.reverser != null" class="list-group-item"><b>Reversed By</b> <a class="float-right">{{ FullName(payment.reverser)}}</a></li>
            </ul>
            <button v-if="payment.status == 1" @click="confirmPayment()" type="button" class="btn btn-primary btn-block"><b>Confirm Payment</b></button>
            <button v-if="payment.status == 1" @click="deletePayment()" type="button" class="btn btn-danger btn-block"><b>Delete Payment</b></button>
            <button v-if="payment.status == 10" @click="reversePayment()" type="button" class="btn btn-danger btn-block"><b>Reverse Payment</b></button>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['refreshPayment'],
    methods:{
        closeModals(){
            $('#confirmFormModal').modal('hide');
            $('#reversalFormModal').modal('hide');
        },
        confirmPayment(){
            $('#confirmFormModal').modal('show');
        },
        deletePayment(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Payment will be deleted and the company ledger will not be updated.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/consultant_practices/payments/'+this.payment.id)
                    .then(response=>{
                        this.$swal.fire('Confirmed!', 'This payment has been deleted', 'success');
                        this.$emit('refreshPayment');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    })
                    .finally(()=>{
                        this.loading = false;
                    });
                }
            });  
        },
        reversePayment(){
            $('#reversalFormModal').modal('show')
        },
        refreshPage(){
            this.closeModals();
            this.$emit('refreshPayment');
        },
    },
    mounted() {},
    props:{
        payment: {type:Object, default: () => {},},
        source: String,
    },
    watch:{}
}
</script>