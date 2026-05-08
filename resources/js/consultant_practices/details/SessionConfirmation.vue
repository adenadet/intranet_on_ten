<template>
<section class="overlay-wrapper p-0">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Confirmation</h3>
        </div>
        <div class="card-body" v-if="confirmation != null">
            <strong><i class="fas fa-book mr-1"></i> Decision</strong>
            <p class="text-muted" v-html="confirmation.decision"></p>
            <hr>
            <strong><i class="fas fa-map-marker-alt mr-1"></i> Confirmer</strong>
            <p class="text-muted">{{ FullName(confirmation.creator) }}</p>
            <hr>
            <strong><i class="fas fa-pencil-alt mr-1"></i> Confirmed At</strong>
            <p class="text-muted">{{ ExcelDate(confirmation.confirmed_at) }}</p>
            <hr>
            <strong><i class="far fa-file-alt mr-1"></i>Description</strong>
            <div class="text-muted" v-html="confirmation.description"></div>
        </div>
        <div class="card-body" v-else>
            <div class="alert alert-info">
                No confirmation record found for this session.
            </div>
        </div>
    </div>
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
    methods: {},
    mounted() {},
    props: {
        confirmation: {type: Object, default: null},
    },
}
</script>