<template>
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Invoice</h3>
                </div>
                <div class="card-body">
                    <CPDetailSession :session.sync="session" :source="type" @refreshSession="getAllInitials"/>
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