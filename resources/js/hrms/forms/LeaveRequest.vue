<template>
<section class="card overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form role="form" @submit.prevent="editMode ? updateLeaveRequest() : createLeaveRequest()">
        <div class="card-body">
            <div class="row" v-if="source != 'mine'">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Employee</label>
                        <div class="form-control" >{{ FullName(user) }}</div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Leave Type</label>
                        <div class="form-control">{{ leave_request.leave_type != null ? leave_request.leave_type.name : 'Undecided' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-if="source == 'mine'">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Leave Type</label>
                        <select class="form-control" name="leave_type_id" id="leave_type_id" v-model="leaveRequestData.leave_type_id">
                            <option value="">--Choose Leave Type--</option>
                            <option v-for="user_leave_type in leave_types" :key="user_leave_type.type_id" :value="user_leave_type.type_id">{{ user_leave_type.leave_type.name }} ({{ user_leave_type.used }} days used out of {{ user_leave_type.leave_type.no_of_days }} days)</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input class="form-control" type="date" placeholder="Start Date" name="from_date" id="from_date" v-model="leaveRequestData.from_date">
                        <has-error :form="leaveRequestData" field="from_date"></has-error> 
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <label>End Date</label>
                        <input class="form-control" type="date" placeholder="End Date" name="to_date" id="to_date" v-model="leaveRequestData.to_date">
                        <has-error :form="leaveRequestData" field="to_date"></has-error> 
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Is Half Day?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="leaveRequestData.is_half_day">
                            <label class="form-check-label">Yes</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 text-danger"><strong>{{ }} days taken</strong></div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label> Reason </label>
                        <input type="text" name="reason" id="reason" class="form-control"  v-model="leaveRequestData.reason" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Attachment</label>
                        <input type="file" name="attachment" id="attachment" class="form-control">
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label> Remarks </label>
                        <textarea type="text" name="remarks" id="remarks" class="form-control" rows=5 placeholder="Other Information" v-model="leaveRequestData.remarks"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                
            </div>     
        </div>
        <div class="card-footer">
            <button type="submit button" class="btn btn-info float-right" >{{ editMode ? 'Update' : 'Create' }} </button>
        </div>
    </form>
</section>
</template>
<script>
import Form from 'vform';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
export default {
    data() {
        return {
            leaveRequestData: new Form({
                id: '',
                leave_type_id: '',
                from_date: '',
                to_date: '',
                reason: '',
                remarks: '',
                is_half_day: false,
                leave_attachment: '',
                leave_allowance: false,
            }),
            leave_request_id: 0,
            leave_types: {},
            loading: false,
            patients: [],
            services: [],
            user: {},
        }
    },
    emits:['refreshPage'],
    mounted() {},
    methods: {
        createLeaveRequest(){
            this.loading = true;
            this.leaveRequestData.post('/api/hrms/leaves')
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                Swal.fire({
                    icon: 'success',
                    title: 'The Leave Request has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });
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
        getAllInitials() {
            this.loading = true;
            axios.get('/api/hrms/leaves/initials')
            .then(response => {
                this.leave_types = response.data.my_leave_types;
                this.loading = false;
            })
            .catch(() => {
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
                this.loading = false;
            });
        },
        updateLeaveRequest(){
            this.loading = true;
            this.leaveRequestData.put('/api/hrms/leaves/'+this.leaveRequestData.id)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                Swal.fire({
                    icon: 'success',
                    title: 'The Leave Request has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });
        },
        
    },
    props: {
        editMode: Boolean,
        leave_request: Object,
        source: String,
    },
    watch:{
        leave_request(){
            this.leaveRequestData.fill(this.leave_request);
            if(this.source == 'mine'){
                this.getAllInitials();
            }
        }
    }
}
</script>