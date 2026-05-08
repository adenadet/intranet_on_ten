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
                                            <option value="pending">Sputum Test Only</option>
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
                        <h3 class="card-title">HMO Office Report {{reportData.report_type == 'all' ? ' - All Appointments' : reportData.report_type == 'sputum' ? ' - Sputum Only' : ''}}</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped text-nowrap" v-if="reportData.report_type == 'all'">
                            <thead>
                                <tr>
                                    <th>Reference ID <br />of Applicant</th>
                                    <th colspan="3">Examination Date</th>
                                    <th>Chest X-ray (CXR) done?</th>
                                    <th>CXR Result</th>
                                    <th>Reason why CXR <br /> was not done?</th>
                                    <th>Sputum Smear <br />Result (1)</th>
                                    <th>Sputum Smear <br />Result (2)</th>
                                    <th>Sputum Smear <br />Result (3)</th>
                                    <th>Sputum Culture <br />Result (1)</th>
                                    <th>Sputum Culture <br />Result (2)</th>
                                    <th>Sputum Culture <br />Result (3)</th>
                                    <th>Drug Sensitivity</th>
                                    <th>Drug Sensitivity <br />Test Details</th>
                                    <th>Signs / Symptoms of TB</th>
                                    <th>Contact to <br />Person with <br />TB</th>
                                    <th>TB Suspected</th>
                                    <th>TB Suspected <br />based on</th>
                                    <th>TB Confirmed</th>
                                    <th>Treatment <br />Started</th>
                                    <th>Reason for <br />certificate not <br />issued</th>
                                    <th>Certificate Number</th>
                                    <th>Medical Certificate<br /> Issued</th>
                                    <th colspan="3">Issue Date of Medical Certificate</th>
                                    <th>FCO region of screening</th>
                                    <th>Name of Clinic</th>
                                    <th>Screening Physician's X-Ray coding</th>
                                    <th>Comments from Screening Physician</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="appointment in appointments">
                                    <td>{{ appointment.patient.passport_no }}</td>
                                    <td>{{ dateDay(appointment.date)}}</td>
                                    <td>{{ dateMonth(appointment.date) }}</td>
                                    <td>{{ dateYear(appointment.date)}}</td>
                                    <td>{{ appointment.report != null ? 'Done' : 'Not Done' }}</td>
                                    <td v-if="appointment.report == null">N/A</td>
                                    <td v-else-if="appointment.report != null && appointment.report.summary == 'normal'">Normal</td>
                                    <td v-else-if="appointment.report != null && appointment.report.summary == 'abnormal'">Abnormal without TB</td>
                                    <td v-else-if="appointment.report != null && appointment.report.summary == 'suggestive'">Abnormal with TB</td>
                                    <td>
                                        <span v-if="appointment.consultation != null &&  appointment.consultation.decision == 6">CXR was done</span>
                                        <span v-else-if="appointment.consultation != null &&  appointment.consultation.decision == 8">Child < 11 years old</span>
                                        <span v-else-if="appointment.consultation != null &&  appointment.consultation.decision == 10 && appointment.consultation.women_pregnant == 1">Pregnant, CXR Deferred: Sputum Smear/Culture Not Done</span>
                                        <span v-else-if="appointment.consultation != null &&  appointment.consultation.decision == 7 && appointment.consultation.women_pregnant == 1">Pregnant, CXR Declined: Sputum Smear/Culture Done</span>
                                        <span v-else-if="appointment.consultation != null &&  appointment.consultation.decision == 10 && appointment.consultation.women_pregnant == 0">Applicant Deferred: Sputum Smear/Culture Not Done</span>
                                        <span v-else-if="appointment.consultation != null &&  appointment.consultation.decision == 7 && appointment.consultation.women_pregnant == 0">Applicant Declined: Sputum Smear/Culture Done</span>
                                        <span v-else>No Show</span>
                                    </td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>
                                        <span v-if="appointment.laboratory != null">{{ appointment.laboratory.summary }}</span>
                                        <span v-else-if="appointment.consultation.decision == 7 && appointment.laboratory == null">Pending</span>
                                        <span v-else-if="appointment.consultation.decision == 7 || appointment.laboratory != null">Sputum Done</span>
                                        <span v-else>Not Done</span>
                                    </td>
                                    <td>
                                        <span v-if="appointment.laboratory != null">{{ appointment.laboratory.summary }}</span>
                                        <span v-else-if="appointment.consultation.decision == 7 && appointment.laboratory == null">Pending</span>
                                        <span v-else-if="appointment.consultation.decision == 7 || appointment.laboratory != null">Sputum Done</span>
                                        <span v-else>Not Done</span>
                                    </td>
                                    <td>
                                        <span v-if="appointment.laboratory != null">{{ appointment.laboratory.summary }}</span>
                                        <span v-else-if="appointment.consultation.decision == 7 && appointment.laboratory == null">Pending</span>
                                        <span v-else-if="appointment.consultation.decision == 7 || appointment.laboratory != null">Sputum Done</span>
                                        <span v-else>Not Done</span>
                                    </td>
                                    <td>Drug Sensitivty Testing Not Done</td>
                                    <td>N/A</td>
                                    <td>
                                        <span v-if="appointment.consultation != null && (appointment.consultation.sym_cough == 1 ||appointment.consultation.sym_fever == 1 || appointment.consultation.sym_haemoptysis == 1 || appointment.consultation.sym_night_sweats == 1 || appointment.consultation.sym_weight_loss == 1)">Yes</span>
                                        <span v-else>No</span>
                                    </td>
                                    <td>
                                        <span v-if="appointment.consultation != null && (appointment.consultation.all_household_tb == 1 || appointment.consultation.all_recent_contact == 1)">Yes</span>
                                        <span v-else>No</span>
                                    </td>
                                    <td>
                                        <span v-if="(appointment.report != null && appointment.report.summmary == 'suggestive') || (appointment.laboratory != null && appointment.laboratory.summmary == 'abnormal')">Yes</span>
                                        <span v-else>No</span>
                                    </td>
                                    <td>
                                        <span v-if="appointment.consultation != null && (appointment.consultation.sym_cough == 1 ||appointment.consultation.sym_fever == 1 || appointment.consultation.sym_haemoptysis == 1 || appointment.consultation.sym_night_sweats == 1 || appointment.consultation.sym_weight_loss == 1 ||appointment.consultation.all_household_tb == 1 || appointment.consultation.all_recent_contact == 1)">History/Examination</span>
                                        <span v-else-if="appointment.report != null && appointment.report.summmary == 'suggestive'">CXR</span>
                                        <span v-else-if="appointment.laboratory != null && appointment.laboratory.summmary == 'abnormal'">Sputum Smear</span>
                                        <span v-else>N/A</span>
                                    </td>
                                    <td>
                                        <span v-if="appointment.consultation != null && (appointment.consultation.decision == 6 && appointment.report != null && appointment.report.summary != 'suggestive')">No</span>
                                        <span v-else-if="appointment.consultation.decision == 8">No</span>
                                        <span v-else-if="appointment.consultation.decision == 7 && appointment.laboratory == null">Pending</span>
                                        <span v-else-if="appointment.consultation.decision == 7 && appointment.laboratory != null && appointment.laboratory.summary == 'normal'">No</span>
                                        <span v-else>Yes</span>
                                    </td>
                                    <td>
                                        <span v-if="appointment.consultation != null && (appointment.consultation.decision == 6 && appointment.report != null && appointment.report.summary != 'suggestive')">No</span>
                                        <span v-else-if="appointment.consultation.decision == 8">No</span>
                                        <span v-else-if="appointment.consultation.decision == 7 && appointment.laboratory == null">Pending</span>
                                        <span v-else-if="appointment.consultation.decision == 7 && appointment.laboratory != null && appointment.laboratory.summary == 'normal'">No</span>
                                        <span v-else>Pending</span>
                                    </td>
                                    <td>
                                        <span v-if="appointment.status == 10 && 
                                        (appointment.laboratory?.summary == 'normal' || appointment.report?.summary == 'normal' || appointment.report?.summary == 'abnormal')">N/A</span>
                                        <span v-else-if="appointment.status == 10 && 
                                        (appointment.laboratory?.summary != 'normal')">Referred for treatment</span>
                                        <span v-else-if="appointment.status == 10 && appointment.consultation?.status == 10">Declined to participate in screening</span>
                                        <span v-else-if="appointment.consultation != null && appointment.consultation.women_pregnant == 1 && appointment.status != 10">Pregnancy-related</span>
                                        <span v-else-if="appointment.consultation != null && appointment.consultation.decision == 7">Pending Sputum Smear or Sputum Culture result</span>
                                        <span v-else-if="appointment.report != null && appointment.report.summary == 'suggestive'">Pending Sputum Smear or Sputum Culture result</span>
                                        <span v-else>Other Reason</span>
                                    </td>
                                    <td>{{ appointment.unique_id }}</td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <span v-if="appointment.status == 10 && 
                                        (appointment.laboratory?.summary == 'normal' || appointment.report?.summary == 'normal' || appointment.report?.summary == 'abnormal')">Issued</span>
                                        <span v-else>Not Issued</span>
                                        {{ appointment.status == 10 ? 'Issued' : 'Not Issued' }}
                                    </td>
                                    <td>{{ dateDay(appointment.issue_at) }}</td>
                                    <td>{{ dateMonth(appointment.issue_at) }}</td>
                                    <td>{{ dateYear(appointment.issue_at) }}</td>
                                    <td>Nigeria</td>
                                    <td>St. Nicholas Hospital</td>
                                    <td><ul v-if="appointment.report != null"><li v-for="finding in appointment.report.findings" :key="finding.id">{{ finding.code}} - {{ finding.name }}</li></ul></td>
                                    <td v-html="appointment.report?.details || 'N/A'"></td>
                                </tr>
                            </tbody>
                        </table>
                        <!--table class="table table-striped text-nowrap" v-else-if="reportData.report_type == 'sputum'">
                            <thead>
                                <tr v-for="appointment in appointments">
                                    <th>Reference ID of Applicant</th>
                                    <th colspan="3">Examination Date</th>
                                    <th colspan="3">Date of Birth</th>
                                    <th>Sputum Smear Result (1)</th>
                                    <th>Sputum Smear Result (2)</th>
                                    <th>Sputum Smear Result (3)</th>
                                    <th>Sputum Culture Result (1)</th>
                                    <th>Sputum Culture Result (2)</th>
                                    <th>Sputum Culture Result (3)</th>
                                    <th>Drug Sensitivity</th>
                                    <th>Drug Sensitivity Test Details</th>
                                    <th>TB Confirmed</th>
                                    <th>Reason for certificate not issued</th>
                                    <th>Certificate Number</th>
                                    <th>Clinic Reference Number</th>
                                    <th>Medical Certificate Issued</th>
                                    <th colspan="3">Issue Date of Medical Certificate</th>
                                    <th>FCO region of screening</th>
                                    <th>Name of Clinic</th>
                                    <th>Comments from Screening Physician</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>Examination Day</td>
                                    <td>Examination Month</td>
                                    <td>Examination Year</td>
                                    <td>Date of Birth Day</td>
                                    <td>Date of Birth Month</td>
                                    <td>Date of Birth Year</td>
                                    <td>Sputum Smear Result (1)</td>
                                    <td>Sputum Smear Result (2)</td>
                                    <td>Sputum Smear Result (3)</td>
                                    <td>Sputum Culture Result (1)</td>
                                    <td>Sputum Culture Result (2)</td>
                                    <td>Sputum Culture Result (3)</td>
                                    <td>Drug Sensitivity</td>
                                    <td>Drug Sensitivity Test Details</td>
                                    <td>TB Confirmed</td>
                                    <td>Reason for certificate not issued</td>
                                    <td>Certificate Number</td>
                                    <td>Medical Certificate Issued</td>
                                    <td colspan="3">Issue Date of Medical Certificate</td>
                                    <td>FCO region of screening</td>
                                    <td>Name of Clinic</td>
                                    <td>Comments from Screening Physician</td>
                                </tr>
                            </tbody>
                        </table-->
                        <table class="" v-else>
                            <tr>
                                <tbody><td colspan="10">Enter Report Parameters</td></tbody>
                            </tr>
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
            this.reportData.post('/api/emr/admin/home_office_report')
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