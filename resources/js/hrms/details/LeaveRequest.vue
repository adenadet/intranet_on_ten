<template>
<section class="content p-0 m-0">
    <div class="card">
        <div class="card-body overlay-wrapper">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12 order-2 order-md-1">
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Leave Type</span>
                                    <span class="info-box-number text-center text-muted mb-0">{{ leave_request.leave_type != null ? leave_request.leave_type.name : 'Invalid Leave Type' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Amount of Days</span>
                                    <span class="info-box-number text-center text-muted mb-0">{{ timeDifference(leave_request.from_date, leave_request.to_date, 'days')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Status</span>
                                    <span class="info-box-number text-center text-muted mb-0">{{leave_request.status == 0 ? 'Unapproved' : (leave_request.status == 1 ? 'Approved' : (leave_request.status == 2 ? 'Ongoing' :(leave_request.status == 3 ? 'Completed ': 'Rejected')))}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h4>Recent Activity</h4>
                            <div class="timeline p-0">
                                <div>
                                    <i class="fas fa-envelope bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time text-white"><i class="fas fa-clock"></i> {{ shortDate(leave_request.created_at) }}</span>
                                        <h3 class="timeline-header bg-blue"><a href="#" class="text-white">{{leave_request.employee != null ? FullName(leave_request.employee.user) : 'Old Staff' }}</a> requested a leave</h3>

                                        <div class="timeline-body">
                                            <p class="text-justify"><h3>Reason:</h3>{{ leave_request.reason }}</p>
                                            <p class="text-justify"><strong>Details:</strong><br />{{ leave_request.remarks }}</p>
                                        </div>
                                        <div class="timeline-footer" v-if="source == 'self'">
                                            <a class="btn btn-primary btn-sm">Read more</a>
                                            <a class="btn btn-danger btn-sm">Delete</a>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="leave_request.approved_by != null">
                                    <i class="fas fa-user bg-green"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> 5 mins ago</span>
                                        <h3 class="timeline-header no-border"><a href="#">{{leave_request.approver != null ? FullName(leave_request.approver.user): 'No Approver Found'}}</a> approved the leave request</h3>
                                    </div>
                                </div>
                                <div v-if="leave_request.approved_by != null">
                                    <i class="fas fa-comments bg-yellow"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> 27 mins ago</span>
                                        <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                                        <div class="timeline-body">
                                            Take me to your leader!
                                            Switzerland is small and neutral!
                                            We are more like Germany, ambitious and misunderstood!
                                        </div>
                                        <div class="timeline-footer">
                                            <a class="btn btn-warning btn-sm">View comment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </div>
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
            leave_request: {},
            loading: false,
        }
    },
    mounted() {},
    methods: {
        getInitials(leave_request_id) {
            this.loading = true;
            axios.get('/api/hrms/leaves/'+leave_request_id+'?type='+this.source)
            .then(response => {
                this.leave_request = response.data.leave_request;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
    },
    props: {
        leave_request_id: Number,
        source: String,
    },
    watch:{
        leave_request_id(){
            this.getInitials(this.leave_request_id);
        }
    }
}
</script>