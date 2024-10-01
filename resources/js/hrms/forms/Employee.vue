<template>
<form @submit.prevent="editMode ? updateEmployee() : createEmployee()">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Supervisor</label>
                <select class="form-control" v-model="employeeData.supervisor_id">{{ (employee.supervisor != null && employee.supervisor.user != null) ? FullName(employee.supervisor.user) : 'No Supervisor'}}

                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Line Manager</label>
                <select class="form-control" v-model="employeeData.reports_to">
                    <option value=""></option>
                    <option v-for="staff in staffs" :key="staff.id" :value="staff.id">{{FullName(staff.user)}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Department</label>
                <select class="form-control" v-model="EmployeeData.department_id">
                    <option value="">--Select Department --</option>
                    <option v-for="department in departments" :value="department.id">{{ department.name }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Designation</label>
                <div class="form-control">{{ (employee.designation != null ) ? employee.designation.name : 'No Designation'}}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Employment Status</label>
                <div class="form-control">{{ (employee.employment_status != null) ? (employee.employment_status == 0 ? 'Inactive' : 'Active') : 'Undetermined'}}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Joined </label>
                <div class="form-control">{{ ExcelDate(employee.date_of_joining)}}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Left </label>
                <div class="form-control">{{ employee.date_of_leaving != null && employee.date_of_leaving != '' ? ExcelDate(employee.date_of_leaving) : 'Still Active'}}</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-sm btn-primary" type="submit">Edit</button>
        </div>
    </div>
</form>
</template>
<script>
export default {
    data() {
        return {
            leaveRequestData: new Form({
                user_id: '',
                employee_id: '',
                office_shift_id: '',
                reports_to: '',
                supervisor_id: '',
                username: '',
                email: '',
                department_id: '',
                sub_department_id: '',
                designation_id: '',
                date_of_joining: '',
                date_of_leaving: '',
                employment_status: '',
            }),
            staffs: [],
        }
    },
    emits:['refreshPage'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        createEmployee(){
            this.loading = true;
            this.employeeData.post('/api/hrms/staffs')
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Leave Request has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
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
            axios.get('/api/hrms/staffs/initials')
            .then(response => {
                this.leave_types = response.data.my_leave_types;
                this.loading = false;
            })
            .catch(() => {
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
                this.loading = false;
            });
        },
        updateEmployee(){
            this.loading = true;
            this.EmployeeData.put('/api/hrms/leaves/'+this.EmployeeData.id)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Employee has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        
    },
    props: {
        editMode: Boolean,
        employee: Object,
    },
    watch:{
        employee(){
            this.EmployeeData.fill(this.employee);
        }
    }
}
</script>