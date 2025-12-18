<template>
    <section class="overlay-wrapper p-0">
        <div class="modal fade" id="sessionFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h4 class="modal-title">{{editMode ? 'Edit Appointment' : 'New Appointment'}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
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
                    <th>Service</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th><button class="btn btn-xs btn-primary float-right" @click="addSession"><i class="fa fa-plus"></i>Add New</button></th>
                </tr>
            </thead>
            <tbody v-if="sessions.length >= 1">
                <tr v-for="session in sessions">
                    <td>{{ExcelDate(session.date)}}</td>
                    <td>183</td>
                    <td>John Doe</td>
                    <td>11-7-2014</td>
                    <td><span class="tag tag-success">Approved</span></td>
                    <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="9">
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
        startSession(dispute){
            this.loading = true;
            this.editMode = false;
            this.dispute = dispute;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        updateSession(session){
            alert(dispute.details);
            this.loading = true;
            this.editMode = true;
            this.dispute = product;
            $('#productModal').modal('show');
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