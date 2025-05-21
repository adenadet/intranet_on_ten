<template>
<section class="content-header">
    <div class="container-fluid">
        <section class="card">
            <div class="card-header bg-navy">Report Query</div>
            <div class="card-body">
                <div class="col-md-12">
                    <form @submit.prevent="searchAppointment()">
                        <alert-error :form="reportData"></alert-error> 
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label>Report Type</label>
                                    <select class="form-control" id="report_type" name="report_type" v-model="reportData.report_type" required>
                                        <option value="">--Select Type of Detailed Report--</option>
                                        <option value="started">All Started Appointments</option>
                                        <option value="completed">All Completed Appointments</option>
                                        <option value="all">All Appointments</option>
                                        <option value="missed">Missed Appointments</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="date" class="form-control" id="date" name="date" v-model="reportData.date" required/>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search mr-1"></i>Search</button>
                    </form>
                </div>
            </div>
        </section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-navy"><h3 class="card-title">Report</h3></div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Applicant ID</th>
                                    <th colspan=3>Examination Date</th>
                                    <th>Chest X-Ray </th>
                                    <th>CXR Result</th>
                                    <th>Reason why CXR was not done?</th>
                                    <th>Sputum Smear Result (1)</th>
                                    <th>Sputum Smear Result (2)</th>
                                    <th>Sputum Smear Result (3)</th>
                                    <th>Sputum Culture Result (1)</th>
                                    <th>Sputum Culture Result (2)</th>
                                    <th>Sputum Culture Result (3)</th>
                                    <th>Drug Sensitivity</th>
                                    <th>Drug Sensitivity Test Details</th>
                                    <th>Signs/Symptoms of TB</th>
                                    <th>Contact to Person with TB</th>
                                    <th>TB Suspected</th>
                                    <th>TB Suspected based on</th>
                                    <th>TB Confirmed</th>
                                    <th>Treatment Started</th>
                                    <th>Reason for certificate not issued</th>
                                    <th>Certificate Number</th>
                                    <th>Clinic Reference Number</th>
                                    <th>Medical Certificate Issued</th>
                                    <th colspan=3>Issue date of Certificate</th>
                                    <th>Country of Screening</th>
                                    <th>Name of Clinic</th>
                                    <th>Screening Physician's X-Ray Coding</th>
                                    <th>Comments from Screening Physician</th>
                                </tr>
                            </thead>
                            <tbody v-if="appointments.length == 0">
                                <tr><td colspan="33" class="text-justify">You have not made any appointments yet</td></tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="(appointment, index) in appointments" :key="index">
                                    <td>{{ addOne(index)}}</td>
                                    <td>{{ appointment.patient_id != null && appointment.patient != null ? appointment.patient.passport_no: 'Deleted User' }}</td>
                                    <td>{{ dateDay(appointment.date)}}</td>
                                    <td>{{ dateMonth(appointment.date)}}</td>
                                    <td>{{ dateYear(appointment.date)}}</td>
                                    <td>{{ appointment.consultation != null ? (appointment.consultation.decision == 6 ? 'Done' : 'Not Done') : 'No Consultation Done' }}</td>
                                    <td>{{ appointment.consultation != null ? (appointment.consultation.decision == 6 ? (appointment.report != null ? (appointment.report.summary == 'normal' ? 'Normal' : (appointment.report.summary == 'not suggestive' ? 'Abnormal without TB' : 'Abnormal with TB')) : 'Pending'): 'N/A') : 'No Consultation Done' }}</td>
                                    <td>{{ appointment.consultation != null ? (appointment.consultation.decision == 6 ? 'CXR Done' : (appointment.consultation.decision != 6 ? (appointment.consultation.decision == 7 ? 'Applicant Declined: Sputum Smear or Culture Done' : (appointment.consultation.decision == 8 ? 'Child < 11 years old' : 'Pregnant, CXR Deferred: Sputum Smear or Culture Not Done')) : 'Unknown')) : 'No Consultation Done' }}</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>{{ appointment.consultation != null ? (appointment.consultation.decision == 8 ? 'N/A' : (appointment.laboratory != null ? appointment.laboratory.summary : (appointment.report != null && appointment.report.summary != 'suggestive' ? 'N/A' : 'Pending')) ) : 'No Consultation Done' }}</td>
                                    <td>{{ appointment.consultation != null ? (appointment.consultation.decision == 8 ? 'N/A' : (appointment.laboratory != null ? appointment.laboratory.summary : (appointment.report != null && appointment.report.summary != 'suggestive' ? 'N/A' : 'Pending')) ) : 'No Consultation Done' }}</td>
                                    <td>{{ appointment.consultation != null ? (appointment.consultation.decision == 8 ? 'N/A' : (appointment.laboratory != null ? appointment.laboratory.summary : (appointment.report != null && appointment.report.summary != 'suggestive' ? 'N/A' : 'Pending')) ) : 'No Consultation Done' }}</td>
                                    <td>{{ appointment.laboratory != null ? 'Sensitivity Not Done' : 'N/A'  }}</td>
                                    <td>&nbsp;</td>
                                    <td>{{ appointment.consultation != null && appointment.consultation.all_previous_tb ? 'Yes' : 'No' }}</td>
                                    <td>{{ appointment.consultation != null && appointment.consultation.all_household_tb ? 'Yes' : 'No' }}</td>
                                    <td>{{ appointment.consultation != null ? ( appointment.consultation.decision == 7 && !(appointment.consultation.women_pregnant) ? 'History/Examination' : ( appointment.report !=null && appointment.report.summary == 'suggestive' ? 'CXR' : 'N/A')) : 'No Consultation Done' }}</td>
                                    <td>{{ appointment.consultation != null ? (appointment.consultation.decision == 7 && !(appointment.consultation.women_pregnant) ? 'History/Examination' : ( appointment.report !=null && appointment.report.summary == 'suggestive' ? 'CXR' : 'N/A')) : 'No Consultation Done' }}</td>
                                    <td>{{ appointment.laboratory != null ?  (appointment.laboratory.summary == normal ? 'No'  :'Yes') : (
                                        appointment.consultation.decision == 8 ? 'No' :
                                        (((appointment.consultation.decision == 7) || (appointment.consultation.decision == 6 && appointment.report != null && appointment.report.summary == 'suggestive')) ? 'Pending' : 'No'))}}
                                    </td>
                                    <td>{{ appointment.laboratory != null && appointment.laboratory.summary != normal ? 'Unknown'  : 'N/A'}}</td>
                                    <td>{{appointment.issuer != null ? 'N/A' : (
                                        appointment.consultation.women_pregnant ? 'Pregnancy-related' : (
                                        appointment.consultation.decision == 10 && !appointment.consultation.woman_pregnant ? 'Declined to participate in screening' : (    
                                        appointment.report != null && appointment.report.summary == 'suggestive' && appointment.laboratory != null && appointment.laboratory.summary == 'suggestive' ? 'Referred for treatment' :
                                        (appointment.report != null && appointment.report.summary == 'suggestive' && appointment.laboratory == null ? 'Pending Sputum Smear ot Sputum Culture' : 'Unknown')))
                                    )}}</td>
                                    <td>{{appointment.unique_id}}</td>
                                    <td>&nbsp;</td>
                                    <td>{{appointment.issuer != null ? 'Issued' : 'Not Issued'}}</td>
                                    <td>{{ dateDay(appointment.issue_at)}}</td>
                                    <td>{{ dateMonth(appointment.issue_at)}}</td>
                                    <td>{{ dateYear(appointment.issue_at)}}</td>
                                    <td>Nigeria</td>
                                    <td>St. Nicholas Hospital</td>
                                    <td v-if="appointment.report != null && appointment.report.findings.length > 0"><span v-for="finding in appointment.report.findings">{{ finding.code }} - {{ finding.name }}</span></td>
                                    <td v-else>&nbsp;</td>
                                    <td v-if="appointment.report != null && appointment.report.findings.length > 0"><div v-html="appointment.report.details"></div></td>
                                    <td v-else>&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
            reportData: new Form({
                report_type: "",
                date: "",
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
            this.reportData.post('/api/emr/admin/detailed_report')
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