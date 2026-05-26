<template>
<section>
    <div class="row">
        <div class="col-md-4">
            <CPDetailPayment :payment.sync="payment" @refreshPayment="getAllInitials()" />
        </div>
        <div class="col-md-8" v-if="payment != null">
            <div class="timeline">
                <div>
                    <i class="fas fa-plus bg-blue"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{timeAgo(payment.created_at)}}</span>
                        <h3 class="timeline-header"><a href="#">{{ FullName(payment.creator) }}</a> created a new payment</h3>
                        <div class="timeline-body" v-html="payment.description"></div>
                    </div>
                </div>
                <div v-if="payment.confirmer != null">
                    <i class="fas fa-check bg-green"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{timeAgo(payment.confirmed_at)}}</span>
                        <h3 class="timeline-header no-border"><a href="#">{{ FullName(payment.confirmer)}}</a> confirmed the payment</h3>
                        <div class="timeline-body" v-html="payment.confirmed_note"></div>
                    </div>
                </div>
                <div v-if="payment.reverser != null">
                    <i class="fas fa-times bg-danger"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{timeAgo(payment.reversed_at)}}</span>
                        <h3 class="timeline-header no-border"><a href="#">{{ FullName(payment.reverser) }}</a> rejected the payment</h3>
                        <div class="timeline-body" v-html="payment.reversed_note"></div>
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
        return {
            editMode: false,
            loading: false,
            payment: {},
            type: 'finance',
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/consultant_practices/payments/'+this.$route.params.id+'?type='+this.type)
            .then(response => {
                this.payment = response.data.payment;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your payment did not load successfully',})
            })
            .finally(() => {
                this.loading = false;
            });
        },
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>