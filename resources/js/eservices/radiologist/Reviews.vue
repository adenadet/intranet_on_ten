<template>
<section class="content-header pt-0">
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <EServiceFormSearch search_type="radiologist" @searchedAppointments="refresh"/>
                <div class="card overlay-wrapper">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <EServiceDetailAppointmentList source="radiologist" :appointments.sync="reports" @refreshAppointments="getAllInitials(current_page)" />
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="reports.per_page != null ? reports.per_page : 52" :records="reports.total != null ? reports.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            current_page: 1,
            loading: false,
            reports: {},
        }
    },
    methods:{
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/emr/radiologists/reviews?page='+page)
            .then(response =>{
                this.refresh(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Reports were loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Reports were not loaded successfully',
                })
            });
        },
        refresh(response){
            this.reports = response.data.reports;
        },
        refreshAppointments(response) {
            this.appointments = response.data.appointments;
            this.services = response.data.services;
            this.nations = response.data.nations;
            this.patients = response.data.patients;
        },
        updateAppointments(response){
            this.reports = response.data.appointments;
        }
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>