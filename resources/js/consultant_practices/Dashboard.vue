<template>
<section>
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
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <section class="col-lg-7">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check mr-1"></i>Sessions
                    </h3>
                    <div class="card-tools">
                        <router-link to="/consultant_practices/front_office/sessions"><button type="button" class="btn btn-xs btn-dark float-right"><i class="fas fa-eye"></i> See All</button></router-link>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <CPDetailSessionList :sessions="sessions.data" source="front_office"/>
                </div>
            </div>
        </section>
        <section class="col-lg-5">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title"><i class="fa fa-user-injured mr-1"></i>Patients</h3>
                    <div class="card-tools">
                        <router-link to="/consultant_practices/front_office/patients"><button type="button" class="btn btn-xs btn-info float-right"><i class="fas fa-eye"></i> See All</button></router-link>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <CPDetailPatientList :patients="patients.data" source="front_office" />
                </div>
            </div>
        </section>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            consultants:{data: [], total: 0,},
            patients:   {data: [], total: 0,},
            payments:   {data: [], total: 0,},
            sessions:   {data: [], total: 0,}, 
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        closeModal(){
            $('#termsModal').modal('hide');
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/consultant_practices/dashboard')
            .then(response => {;
                this.consultants = response.data.consultants;
                this.patients = response.data.patients;
                this.payments = response.data.payments;
                this.consultants = response.data.consultants;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        
    },
}
</script>