<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="card-title">Report Parameters</h4>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="searchAppointment()">
                            <alert-error :form="reportData"></alert-error> 
                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Report Type</label>
                                        <select class="form-control" id="report_type" name="report_type" v-model="reportData.report_type" required>
                                            <option value="">--Select Type of Detailed Report--</option>
                                            <option value="all">All Appointments</option>
                                            <option value="sputum">Sputum Test Only</option>
                                            <option value="not suggestive">Not Suggestive Only</option>
                                            <option value="normal">Normal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Start Date:</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" v-model="reportData.start_date" required/>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>End Date:</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" v-model="reportData.end_date" required/>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search mr-1"></i>Search</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Radiologist Report </h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Unique ID</th>
                                    <th>Xray Decision</th>
                                    <th>Lab Report</th>
                                    <th>Gender</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(appointment, index) in appointments" :key="appointment.id">
                                    <td>{{ addOne(index) }}</td>
                                    <td>{{ appointment.unique_id }}</td>
                                    <td>{{ appointment.report != null ? appointment.report.summary : 'N/A' }}</td>
                                    <td>{{ appointment.laboratory != null ? appointment.laboratory.summary : 'N/A' }}</td>
                                    <td>{{ appointment.patient != null ? appointment.patient.sex : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            appointments:[],
            loading: false,
            reportData: new Form({
                end_date: "",
                report_type: "",
                start_date: "",
            }),
        }
    },
    mounted() {},
    methods: {
        refreshAppointment(response) {
            this.appointments = response.data.appointments;
        },
        searchAppointment(){
            this.loading = true;
            this.reportData.post('/api/emr/admin/radiologist_report')
            .then(response => {
                this.appointments = response.data.appointments;
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
            });
        }
    },
}
</script>