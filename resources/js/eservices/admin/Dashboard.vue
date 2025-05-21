<template>
 <section class="content overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-lg-6 col-6">
            <div class="small-box bg-info">
                <div class="inner"><h3>{{ all }}</h3><p>All Appointments</p></div>
                <div class="icon"><i class="fa fa-copy"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner"><h3>{{ started }}</h3><p>Started Appointments</p></div>
                <div class="icon"><i class="fa  fa-file-alt"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-danger">
                <div class="inner"><h3>{{ missed }}</h3><p>Missed Appointments</p></div>
                <div class="icon"><i class="fa fa-file-excel"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-lightblue">
                <div class="inner"><h3>{{ xray }}</h3><p>Sent for X-ray</p></div>
                <div class="icon"><i class="fa fa-file-pdf"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner"><h3>{{ child }}</h3><p>Minors</p></div>
                <div class="icon"><i class="fa fa-file-image"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner"><h3>{{ sputum }}</h3><p>Sent for Sputum</p></div>
                <div class="icon"><i class="fa fa-file-code"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple">
                <div class="inner"><h3>{{ postponed }}</h3><p>Postponed</p></div>
                <div class="icon"><i class="fa fa-file-export"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <section class="col-lg-12 connectedSortable">
            <div class="card">
                <div class="card-header bg-navy">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i>Last Month Summary</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 450px;">
                    <table class="table table-head-fixed table-striped table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>X-ray</th>
                                <th>Sputum</th>
                                <th>Kids under 11</th>
                                <th>Postponed</th>
                                <th>Missed</th>
                            </tr>
                        </thead>
                        <tbody v-if="reports.length == 0">
                            <tr><td colspan="8" class="text-center">No report yet. Fill the form please</td></tr>
                        </tbody>
                        <tbody v-else>
                            <tr v-for="(report, index) in reports" :key="index">
                                <td>{{addOne(index)}}</td>
                                <td>{{ExcelDate(report.date) }}</td>
                                <td>{{report.total}}</td>
                                <td>{{report.x_ray}}</td>
                                <td>{{report.sputum}}</td>
                                <td>{{report.kid_under_11}}</td>
                                <td>{{report.postponed}}</td>
                                <td>{{report.missed}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            all: 0,
            child: 0,
            loading: false,
            missed: 0,
            postponed: 0,
            reports: [],
            sputum: 0,
            started: 0,
            xray: 0,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/emr/admin/dashboard?page='+page)
            .then(response => {
                this.refreshAppointments(response); 
                this.loading = false;})
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',});
                this.loading = false;
            });
        },
        refreshAppointments(response) {
            this.all = response.data.all;
            this.child = response.data.child;
            this.missed = response.data.missed;
            this.postponed = response.data.postponed;
            this.reports = response.data.reports;
            this.sputum = response.data.sputum;
            this.started = response.data.started;
            this.xray = response.data.xray;
        },
        searchAppointment(){
            this.loading = true;
            this.reportData.post('/api/emr/admin/dashboard')
            .then(response => {
                this.refreshAppointment(response);
                this.loading = false;
            })
            .close(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                    });
                this.loading = false;
                }
            );
        }
    },

}
</script>