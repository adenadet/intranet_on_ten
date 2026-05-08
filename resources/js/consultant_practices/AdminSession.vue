<template>
<section class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header p-0">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                    <li class="pt-2 px-3"><h3 class="card-title">Session Detail</h3></li>
                    <li class="nav-item">
                        <a class="nav-link active" id="basic-tab" data-toggle="pill" href="#basic" role="tab" aria-controls="basic" aria-selected="true">Front Office</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="payment-confirmation-tab" data-toggle="pill" href="#payment-confirmation" role="tab" aria-controls="payment-confirmation" aria-selected="false">Customer Payment Confirmation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="service-confirmation-tab" data-toggle="pill" href="#service-confirmation" role="tab" aria-controls="service-confirmation" aria-selected="false">Service Confirmation</a>
                    </li>
                </ul>
            </div>
            <div class="card-body overlay-wrapper">
                <div class="tab-content" id="custom-tabs-two-tabContent">
                    <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                        <CPDetailSession :session.sync="session" :source="type" @refreshSession="getAllInitials"/> 
                    </div>
                    <div class="tab-pane fade" id="payment-confirmation" role="tabpanel" aria-labelledby="payment-confirmation-tab">
                        <CPDetailSessionPaymentConfirmation :payment.sync="payment" :source="type" @refreshSession="getAllInitials"/> 
                    </div>
                    <div class="tab-pane fade" id="service-confirmation" role="tabpanel" aria-labelledby="service-confirmation-tab">
                        <CPDetailSessionConfirmation :confirmation.sync="confirmation" :source="type" @refreshSession="getAllInitials"/> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            confirmation: null,
            payment: null,
            query: '',
            session:   {data: [], total: 0,},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        closeModal(){
            $('#termsModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/consultant_practices/sessions/'+this.$route.params.id)
            .then(response => {;
                this.session = response.data.session;
                this.payment = response.data.payment;
                this.confirmation = response.data.confirmation;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            })
            .finally(() => {
                this.loading = false;
            });
        },
        
    },
}
</script>