<template>
    <section class="overlay-wrapper p-0">
        <div class="modal fade" id="confirmPaymentFormModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h4 class="modal-title">Confirm Payment</h4>
                        <button type="button" @click="closeModals" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <CPFormSessionPaymentConfirmation :editMode="editMode" :session.sync="session" @refreshPaymentConfirmation="refreshSessionList"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmServiceFormModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h4 class="modal-title">Confirm Service</h4>
                        <button type="button" @click="closeModals" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <CPFormSessionConfirmation :editMode="editMode" :session.sync="session" @refreshSessionConfirmation="refreshSessionList"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="sessionFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h4 class="modal-title">{{editMode ? 'Edit Appointment' : 'New Appointment'}}</h4>
                        <button type="button" @click="closeModals" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <CPFormSession :editMode="editMode" :session.sync="session" @refreshSessionForm="refreshSessionList"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <table class="table table-head-fixed text-nowrap table-striped ">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Specialty</th>
                    <th>Consultant </th>
                    <th>Patient</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th v-if="source != 'finance'">Payment Status</th>
                    <th v-if="source != 'finance'">Service Status</th>
                    <th><button class="btn btn-xs btn-primary float-right" @click="addSession"><i class="fa fa-plus"></i>Add New</button></th>
                </tr>
            </thead>
            <tbody v-if="sessions.length >= 1">
                <tr v-for="session in sessions" :key="session.id">
                    <td>{{ExcelDate(session.date)}}</td>
                    <td>{{session.consultant?.specialty?.name || 'N/A'}}</td>
                    <td>{{ session.consultant ? `${session.consultant.title}. ${session.consultant.first_name} ${session.consultant.last_name}` : 'N/A' }}</td>
                    <td>{{session.patient?.name || 'N/A'}}</td>
                    <td>{{session.patient != null ?(session.patient.patient_type == 'hmo' ? 'Insurance': 'Private') : 'N/A'}}</td>
                    <td>{{currency(session.amount)}}</td>
                    <td v-if="source != 'finance'">
                        <span class="badge badge-success" v-if="session.payment_status == 10">Confirmed</span>
                        <span class="badge badge-warning" v-else-if="session.payment_status == 0">Unconfirmed</span>
                        <span class="badge badge-danger" v-else>Rejected</span>
                    </td>
                    <td v-if="source != 'finance'">
                        <span class="badge badge-success" v-if="session.service_status == 1">Confirmed</span>
                        <span class="badge badge-warning" v-else-if="session.service_status == 0">Unconfirmed</span>
                        <span class="badge badge-danger" v-else>Rejected</span>
                    </td>
                    <td>
                        <span class="nav-link" data-toggle="dropdown" href="#"><i class="fa fa-ellipsis-v"></i></span>
                        <div v-if="source == 'admin'" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link class="btn btn-block dropdown-item" :to="'/consultant_practices/admin/sessions/'+ session.id"><i class="fas fa-eye mr-2 text-success"></i> View Session</router-link>
                            <button class="btn btn-block dropdown-item" @click="updateSession(session)"><i class="fas fa-edit mr-2 text-primary"></i> Update Session</button>
                            <button class="btn btn-block dropdown-item" @click="deactivateSession(session.id)"><i class="fas fa-times mr-2 text-danger"></i> {{session.status == 1 ? 'Deactivate' : 'Reactivate'}} Service</button>
                        </div>
                        <div v-if="source == 'finance'" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link class="btn btn-block dropdown-item" :to="'/consultant_practices/finance/invoices/'+ session.id"><i class="fas fa-eye mr-2 text-success"></i> View Session</router-link>
                            <button class="btn btn-block dropdown-item" @click="viewInvoice(session)"><i class="fas fa-file-pdf mr-2 text-primary"></i> View Invoice</button>
                        </div>
                        <div v-if="source == 'front'" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link class="btn btn-block dropdown-item" :to="'/consultant_practices/front/sessions/'+ session.id"><i class="fas fa-eye mr-2 text-success"></i> View Session</router-link>
                            <button v-if="session.payment_status == 0" class="btn btn-block dropdown-item" @click="confirmPayment(session)"><i class="fas fa-cash-register mr-2 text-primary"></i> Confirm Session Payment</button>
                            <button class="btn btn-block dropdown-item" @click="updateSession(session)"><i class="fas fa-edit mr-2 text-primary"></i> Update Session</button>
                            <button class="btn btn-block dropdown-item" @click="deactivateSession(session.id)"><i class="fas fa-times mr-2 text-danger"></i> {{session.status == 1 ? 'Deactivate' : 'Reactivate'}} Service</button>
                        </div>
                        <div v-if="source == 'medical'" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link class="btn btn-block dropdown-item" :to="'/consultant_practices/medical/sessions/'+ session.id"><i class="fas fa-eye mr-2 text-success"></i> View Session</router-link>
                            <button v-if="session.service_status == 0" class="btn btn-block dropdown-item" @click="confirmService(session)"><i class="fas fa-check-double mr-2 text-primary"></i> Confirm Session Service</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td :colspan=" source != 'finance'? 9 : 7">
                        No Session meets your requirements.
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            session: {},
        }
    },
    emits: ['refreshSessionList'],
    methods:{
        addSession(){
            this.loading = true;
            this.editMode = false;
            this.dispute = {};
            $('#sessionFormModal').modal('show');
            this.loading = false; 
        },
        closeModals(){
            $('#confirmServiceFormModal').modal('hide');
            $('#confirmPaymentFormModal').modal('hide');
            $('#sessionFormModal').modal('hide');
        },
        confirmService(session){
            this.loading = true;
            this.editMode = false;
            this.session = session;
            if (session.payment_status == 0){
                this.$swal.fire({
                title: 'Are you sure?',
                text: "This Session payment has not been confirmed. Please confirm the payment before confirming the service.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm service!'
                })
                .then((result) => {
                    if(result.value){
                        $('#confirmServiceFormModal').modal('show');
                    }
                });
            } 
            else {
                $('#confirmServiceFormModal').modal('show');
            }
            this.loading = false;
        },
        confirmPayment(session){
            this.loading = true;
            this.editMode = false;
            this.session = session;
            $('#confirmPaymentFormModal').modal('show');
            this.loading = false;
        },
        deactivateSession(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Session will no longer be available to people who visit your page",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, deactivate it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/consultant_practices/sessions/'+id)
                    .then(response=>{
                        this.$swal.fire('Deactivated!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        refreshSessionList(){
            this.closeModals();
            this.$emit('refreshSessionList');
        },
        updateSession(session){
            this.loading = true;
            this.editMode = true;
            this.session = session;
            $('#sessionFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        sessions: Array,
        source: String,
    },
    watch:{}
}
</script>