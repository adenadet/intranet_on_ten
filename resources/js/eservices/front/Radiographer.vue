<template>
<section>
    <div class="row">
        <div class="col-12">
            <EServiceFormSearch />
            <div class="card overlay-wrapper">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <EServiceDetailAppointmentList source="radiographer" :appointments.sync="appointments" />
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="appointments.per_page != null ? appointments.per_page : 52" :records="appointments.total != null ? appointments.total : 550" ></pagination>
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
            appointments: {},
            current_page: 1,
            editMode: true,
            form: new Form({}),
            loading: false,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        confirmAppointment(id){
            Swal.fire({
                title: 'Are you sure the Xray has been done?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, it has been done!'
                })
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.put('/api/emr/appointments/xray/'+id)
                    .then(response=>{
                        Swal.fire('Confirmed!', 'The Xray has been confirmed.', 'success');
                        this.refreshAppointments(response); 
                        this.loading = false;  
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                        this.loading = false;
                    });
                }
            });
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/emr/appointments/xray?page='+page)
            .then(response => {this.refreshAppointments(response); this.loading = false;})
            .catch(() => {
                this.loading = false;
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        refreshAppointments(response) {
            this.appointments = response.data.appointments;
        },
    },
    props: {}
}
</script>