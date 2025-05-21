<template>
<section class="overlay-wrapper">
    <div class="modal fade" id="EmployeeModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{editMode ? 'Update ': 'Create New '}} Employee</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployee :employee.sync="employee" :editMode.sync="editMode" @reloadUser="reloadPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Supervisor</label>
                <div class="form-control">{{ (employee.supervisor != null && employee.supervisor.user != null) ? FullName(employee.supervisor.user) : 'No Supervisor'}}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Line Manager</label>
                <div class="form-control">{{ (employee.line_manager != null && employee.line_manager.user != null) ? FullName(employee.line_manager.user) : 'No Supervisor'}}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Department</label>
                <div class="form-control">{{ (employee.department) ? employee.department.name : 'No Department Assigned'}}</div>
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
                <div class="form-control">{{ (employee.employment_status != null) ? (employee.employment_status == 0 ? 'Inactive' : (employee.employment_status == 1 ? 'Active': (employee.employment_status == 2 ? 'Resigned': (employee.employment_status == 3 ? 'Terminated': (employee.employment_status == 4 ? 'Deceased': (employee.employment_status == 5 ? 'Retired': 'Undetermined')))))) : 'Undetermined' }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Email </label>
                <div class="form-control">{{ employee.email}}</div>
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
            <button class="btn btn-sm btn-primary" @click="editEmployee"><i class="fa fa-edit mr-1"></i>Edit</button>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            editting: true,
            loading: false,
        }
    },
    emits:['employeeReload'],
    mounted() {},
    methods: {
        closeModal(){
            $('#EmployeeModal').modal('hide');
        },
        editEmployee(){
            this.loading = true;
            this.editMode = true;
            $('#EmployeeModal').modal('show');
            this.loading = false;
        },
        reloadPage(){
            this.$emit('reloadPage');
        }
    },
    props: {
        employee: Object,
        source: String,
    },
    watch:{
        employee(){

        }
    }
}
</script>