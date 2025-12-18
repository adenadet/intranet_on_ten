<template>
    <section class="overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <table class="table table-head-fixed text-nowrap table-striped ">
            <thead>
                <tr>
                    <th>Unique ID</th>
                    <th>Patient</th>
                    <th>Sex</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="patients.length > 0">
                <tr v-for="patient in patients">
                    <td>{{ patient.unique_id }}</td>
                    <td>{{ patient.name }}</td>
                    <td>{{ age(patient.dob)}}</td>
                    <td>{{ patient.status }}</td>
                    <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="5">No Patient meets your requirements</td>
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
            patient: {},
        }
    },
    methods:{
        addPatient(){
            this.loading = true;
            this.editMode = false;
            this.patient = {};
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
        patients: Array,
        source: String,
    },
    watch:{}
}
</script>