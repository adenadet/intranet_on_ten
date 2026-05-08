<template>
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Fixed Header Table</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 300px;">
                <CPDetailPayments :payments.sync="payments.data" />
            </div>
            <div class="card-footer">
                <div class="col-md-12">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="patients.per_page != null ? patients.per_page : 52" :records="patients.total != null ? patients.total : 550" ></pagination>
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
            type: '',
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
            axios.get('/api/consultant_practices/payments?end_date='+this.end_date+'query='+this.query+'&start_date='+this.start_date+'&type='+this.type)
            .then(response => {
                this.patients = response.data.patients;
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