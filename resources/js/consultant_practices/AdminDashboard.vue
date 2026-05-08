<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ consultants.total }}</h3>
                    <p>External Consultants</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-md text-white"></i>
                </div>
                <a href="/consultant_pratices/admin/consultants" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ patients.total }}</h3>
                    <p>Patients</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-injured text-white"></i>
                </div>
                <a href="/consultant_pratices/admin/patients" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ sessions.total }}</h3>
                    <p>Sessions</p>
                </div>
                <div class="icon">
                    <i class="fa fa-calendar-check text-white"></i>
                </div>
                <a href="/consultant_pratices/admin/sessions" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ payments.total }}</h3>
                    <p>Payments</p>
                </div>
                <div class="icon">
                    <i class="fa fa-cash-register text-white"></i>
                </div>
                <a href="/consultant_pratices/admin/payments" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Consultants</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <CPDetailConsultantList :consultants.sync="consultants.data" :source="type" @refreshConsultantList="getAllInitials" />
                </div>
                <div class="card-footer">
                    <router-link class="btn btn-tool text-dark" to="/consultant_practices/admin/consultants">See All >>></router-link>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Patients</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <CPDetailPatientList :patients.sync="patients.data" />
                </div>
                <div class="card-footer">
                    <router-link class="btn btn-tool text-dark" to="/consultant_practices/admin/patients">See All >>></router-link>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Services</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <CPDetailServiceList :services="services.data" @refreshServiceList="getAllInitials()" />
                </div>
                <div class="card-footer">
                    <router-link class="btn btn-tool text-dark" to="/consultant_practices/admin/services">See All >>></router-link>
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
            services: {data: [], total: 0,},
            sessions: {data: [], total: 0,},
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
            axios.get('/api/consultant_practices/dashboard?type=admin')
            .then(response => {
                this.consultants = response.data.consultants ?? {data: [], total: 0,};
                this.companies = response.data.companies ?? {data: [], total: 0,};
                this.invoices = response.data.invoices ?? {data: [], total: 0,};
                this.patients = response.data.patients ?? {data: [], total: 0,};
                this.payments = response.data.payments;
                this.services = response.data.services ?? {data: [], total: 0,};
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