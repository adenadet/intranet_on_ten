<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Payment Details</label>
                    <div class="">
                        {{ payment.account?.bank?.bank_name }} {{ payment.account?.account_name }} {{ payment.account?.accouny_number }}<br> 
                        <strong>{{ payment.company?.name }}</strong> - {{ ExcelDate(payment.date) }} ({{ currency(payment.amount) }})
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor class="form-control" theme="snow" content-type="html" v-model:content="reversalData.description"/>
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-danger" :disabled="loading" @click="rejectPayment">
                    <i class="fas fa-times mr-1"></i>{{ loading ? 'Processing...' : 'Reverse Payment' }}
                </button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            accounts: [],
            companies: [],
            loading: false,
            reversalData: new Form({
                payment_id: '',
                description: '',
            }),
        };
    },
    emits:['refreshPaymentReversalForm'],
    methods: {
        rejectPayment(){
            this.loading = true;
            this.reversalData.put('/api/consultant_practices/payments/'+this.payment.id+'/reverse')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Reversed',
                    text: 'The payment was reversed successfully',
                });
                this.$emit('refreshPaymentReversalForm', response);
            })
            .catch(error=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: error.message
                });
            })
            .finally(()=>{
                this.loading = false;
            });
        },
    },
    props:{
        payment: {type: Object, default: null},
    },
    watch:{
        payment(){
            if (this.payment != null){
                this.reversalData.payment_id = this.payment.id;
            }
            else{
                this.resetForm()
            }
        }
    }
};
</script>