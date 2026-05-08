<template>
<section class="">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payments</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height:500px;">
                    <CPDetailPaymentList :payments.sync="payments.data" :source="type" @refreshPaymentList="getAllInitials" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="payments.per_page != null ? payments.per_page : 52" :records="payments.total != null ? payments.total : 550" ></pagination>
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
            end_date: '',
            query: '',
            payments:   {data: [], total: 0,},
            start_date: '',
            status: '',
            type: 'admin',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        closeModal(){
            $('#Modal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/consultant_practices/payments?type='+this.type+'&status='+this.status+'&start_date='+this.start_date+'&end_date='+this.end_date+'&query='+this.query)
            .then(response => {
                this.payments = response.data.payments ?? {data: [], total: 0,};
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Payments did not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
    },
}
</script>