<template>
<section class="p-0">
    <div class="modal fade" id="requestFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Request Detail</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormLeaveRequest :editMode.sync="editMode" :leave_request.sync="leave_request" :source="source" @refreshPage="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="requestModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Request Detail</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true text-white">&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <HrmsDetailLeaveRequest :leave_request_id.sync="leave_request_id" :source="source" />
                </div>
            </div>
        </div>
    </div>
    <div class="card-header bg-navy">
        <h3 class="card-title">All Leave Requests</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-success" @click="requestLeave" v-if="source == 'mine'"><i class="fa fa-plus"></i> Request Leave</button>
            <button class="btn btn-sm btn-primary" @click="addAppointment" v-if="source == 'front_admin'"><i class="fa fa-calendar-plus"></i> Book Appointment</button>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap table-striped">
            <thead class="bg-dark">
                <tr>
                    <th></th>
                    <th>Staff</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Requested On</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="requests.data == null || requests == null">
                <tr>
                    <td colspan="8" class="text-center">You have not made any requests yet</td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr v-for="(request, index) in requests.data" :key="request.id">
                    <td>{{addOne(index)}}</td>
                    <td>{{request.employee != null ? FullName(request.employee.user) : 'Deactivated Staff'}}</td>
                    <td>{{request.leave_type_id != null && request.leave_type != null ? request.leave_type.name : ''}}</td>
                    <td>{{ExcelDate(request.from_date) }}</td>
                    <td>{{ExcelDate(request.to_date) }}</td>
                    <td>{{ExcelDate(request.updated_at) }}</td>
                    <td>{{request.status == 0 ? 'Unapproved' : (request.status == 1 ? 'Approved' : (request.status == 2 ? 'Ongoing' :(request.status == 3 ? 'Completed ': 'Rejected')))}}</td>
                    <td>
                        <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'admin'">
                            <button class="dropdown-item btn btn-block btn-sm" @click="viewRequest(request.id)"><i class="fa fa-eye mr-1 text-primary"></i> View request</button>
                        </div>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'mine'">
                            <button class="dropdown-item btn btn-block btn-sm" @click="viewRequest(request.id)"><i class="fa fa-eye mr-1 text-primary"></i> View request</button>
                        </div>
                        
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
            form: new Form({}),
            leave_request: {},
            leave_request_id: 0,
            user: {},
        }
    },
    mounted() {},
    methods: {
        closeModals(){
            $('#appointmentModal').modal('hide');
            $('#patientModal').modal('hide');
            $('#applicantModal').modal('hide');
            $('#receiptModal').modal('hide');
        },
        deleteAppointment(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/emr/appointments/'+id)
                    .then(response=>{
                    Swal.fire('Deleted!', 'Appointment has been deleted.', 'success');
                    this.refreshAppointments(response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        refreshPage(response){
            this.getAllInitials();
        },
        requestLeave(){
            this.leave_request = {};
            this.editMode = false;
            $('#requestFormModal').modal('show');
        },
        rescheduleAppointment(appointment) {
            this.editMode = true;
            this.appointment = appointment;
            this.$emit('AppointmentDataFill', this.appointment);
            $('#appointmentModal').modal('show');
            this.loading = false;
        },
        resendAppointment(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "The candidate would get a mail with the confirmation letter",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resend confirmation!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.get('/api/emr/registrations/resend/'+id)
                    .then(response=>{
                        //if (response.data.status == 'error')
                        this.$swal.fire(response.data.status, response.data.message, response.data.status);
                        //this.refreshAppointments(response);   
                    })
                    .catch(()=>{
                    this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        viewPayment(appointment){
            this.appointment = appointment;
            $('#receiptModal').modal('show');
        },
        viewRequest(id){
            this.leave_request_id = id;
            $('#requestModal').modal('show');
        }
    },
    props: {
        requests: Object,
        source: String,
    },
    watch:{
        source(){
            if (source == 'mine'){

            }
        }
    }
}
</script>