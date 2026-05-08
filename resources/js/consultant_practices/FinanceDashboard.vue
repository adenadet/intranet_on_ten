<template>
<section class="">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ sessions.total }}</h3><p>Invoices</p>
                </div>
                <div class="icon">
                    <i class="fa fa-file-invoice"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner"><h3>{{patients.total}}<!--sup style="font-size: 20px">%</sup--></h3><p>Patients</p></div>
                <div class="icon"><i class="fa fa-user-injured"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner"><h3>{{consultants.total}}</h3><p>Consultants</p></div>
                <div class="icon"><i class="fa fa-users"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ companies.total }}</h3><p>Companies</p>
                </div>
                <div class="icon">
                    <i class="fa fa-file"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Invoices</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 400px;">
                    <CPDetailSessionList :sessions.sync="sessions.data" source="finance" />
                </div>
                <div class="card-footer">
                    <router-link to="/consultant_practices/finance/invoices"> See All</router-link>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payments</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 400px;">
                    <CPDetailPaymentList :payments="payments.data" />
                </div>
                <div class="card-footer">
                    <router-link to="/consultant_practices/finance/invoices"> See All</router-link>
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
            companies: {data: [], total: 0,},
            consultants: {data: [], total: 0,},
            invoices: {data: [], total: 0,},
            payments: {data: [], total: 0,},
            patients: {data: [], total: 0,},
            sessions: {data: [], total: 0,},
            start_date: '',
            status: '',
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
            axios.get('/api/consultant_practices/dashboard?type=finance')
            .then(response => {
                this.consultants = response.data.consultants ?? {data: [], total: 0,};
                this.companies = response.data.companies ?? {data: [], total: 0,};
                this.invoices = response.data.invoices ?? {data: [], total: 0,};
                this.patients = response.data.patients ?? {data: [], total: 0,};
                this.payments = response.data.payments;
                this.sessions = response.data.sessions ?? {data: [], total: 0,};
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        
    },
}
</script>