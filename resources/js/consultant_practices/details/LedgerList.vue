<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
<table class="table table-head-fixed table-striped text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th v-if="source != 'company'">Company</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Balance</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody v-if="ledgers.length > 0">
            <tr v-for="(ledger, index) in ledgers" :key="ledger.id">
                <td>{{ ExcelDate(ledger.date) }}</td>
                <td v-if="source != 'company'">{{ ledger.company?.name }}</td>
                <td>{{ firstUp(ledger.type) }}</td>
                <td>{{ ledger.amount }}</td>
                <td>{{ ledger.balance }}</td>
                <td>{{ ledger.reference_type }} - {{ ledger.reference_id }}</td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="6">No Consultant meets your requirements</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            ledger: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['refreshConsultantList'],
    methods:{
        addConsultant(){
            this.loading = true;
            this.editMode = false;
            this.ledger = {services:[],};
            $('#consultantFormModal').modal('show');
            this.loading = false; 
        },
        closeModals(){
            $('#consultantFormModal').modal('hide');
        },
        deactivateConsultant(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Consultant will no longer be available",
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
                    this.form.delete('/api/consultant_practices/consultants/'+id)
                    .then(response=>{
                        this.$swal.fire('Deactivated!', response.data.message, 'success');
                        this.refreshPage();
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        refreshPage(){
            this.closeModals();
            this.$emit('refreshConsultantList');
        },
        updateConsultant(consultant){
            this.loading = true;
            this.editMode = true;
            this.consultant = consultant;
            $('#consultantFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        ledgers: Array,
        source: String,
    },
    watch:{}
}
</script>