<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    
    <form @submit.prevent="submit">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Session Details</label>
                    <div class="card p-3">
                        <p><strong>Date:</strong> {{ session ? new Date(session.date).toLocaleString() : 'Loading...' }}</p>
                        <p><strong>Consultant:</strong> {{ session?.consultant?.title }} {{ session?.consultant?.first_name }} {{ session?.consultant?.last_name }}</p>
                        <p><strong>Patient:</strong> {{ session?.patient?.name }}</p>
                        <p><strong>Amount:</strong> {{ session ? currency(session.amount) : 'Loading...' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Decision</label>
                    <select class="form-control" v-model="confirmationData.decision">
                        <option value="confirm">Confirm Service</option>
                        <option value="reject">Reject Service</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Notes (optional for confirm, required for rejection)</label>
                    <QuillEditor class="form-control" rows="4" content-type="html" theme="snow" v-model:content="confirmationData.description" :required="confirmationData.decision === 'reject'"></QuillEditor>
                </div>
            </div>
            
            <div class="col-md-12">
                <div v-if="confirmationData.decision === 'reject'" class="alert alert-warning">
                    You are about to reject this session. This action cannot be undone.
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn" :class="confirmationData.decision === 'reject' ? 'btn-danger' : 'btn-success'" :disabled="!confirmationData.decision || loading">
                    <span v-if="loading"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                    <span v-else>Submit Decision</span>
                </button>
            </div>
        </div>

    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            confirmationData: new Form({
                session_id: null,
                decision: null,
                description: ''
            }),
            loading: false,
        }
    },

    methods: {
        async submit() {
            if (this.confirmationData.decision === 'reject' && !this.confirmationData.description) {
                alert('Description is required for rejection')
                return
            }

            this.loading = true;
            this.confirmationData.post('/api/consultant_practices/sessions/confirm_service')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'The Session Service was '+ (this.confirmationData.decision === 'reject' ? 'rejected' : 'confirmed') +' successfully',
                });
                this.$emit('refreshSession', response);
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            })
            .finally(()=>{
                this.loading = false;
            });
        }
    },
    mounted() {},
    props: {
        session: Object,
    },
    watch:{
        session(){
            this.confirmationData.session_id = this.session.id;
        }
    }
}
</script>