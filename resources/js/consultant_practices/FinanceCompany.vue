<template>
<section class="">
    <div class="row">
        <div class="col-md-4">
            <CPDetailCompany :company.sync="company" :source="type" @refreshCompany="getAllInitials" />

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Accounts</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 200px;">
                    <CPDetailAccountList :accounts.sync="company.accounts" :company.sync="company" :source="type" @refreshAccountList="getAllInitials" />
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ledger</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 400px;">
                    <CPDetailLedgerList :ledgers.sync="company.ledgers" source="company" @refreshLedgerList="getAllInitials" />
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Consultants</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 400px;">
                    <CPDetailConsultantList :consultants.sync="company.consultants" source="company" @refreshConsultantList="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            end_date: '',
            query: '',
            company: {accounts:[], consultants: [],},
            companies:   {data: [], total: 0,},
            ledgers: [],
            start_date: '',
            status: '',
            type: 'finance',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        closeModal(){
            $('#Modal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/consultant_practices/companies/'+this.$route.params.id)
            .then(response => {
                this.company = response.data.company ?? {accounts: [], consultants: [],};
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Company did not load successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
    },
}
</script>