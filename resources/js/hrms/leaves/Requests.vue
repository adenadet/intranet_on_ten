<template>
<section class="content-header pt-0">
    <div class="row">
        <div class="col-12">
            <div class="card overlay-wrapper">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <HrmsDetailLeaveRequestList source="mine" :requests.sync="requests" />
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="requests.per_page != null ? requests.per_page : 52" :records="requests.total != null ? requests.total : 550" ></pagination>
                </div>
            </div>
        </div>
    </div>
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
            current_page: 1,
            editMode: true,
            loading: false,
            request: {},
            requests: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/leaves?type=mine&page='+page)
            .then(response => {
                this.requests = response.data.requests;
                this.loading = false;
                console.log(this.requests);
            })
            .catch(() => {
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Your consultations did not loaded successfully',
                })
            });
        },
    },
    props: {}
}
</script>