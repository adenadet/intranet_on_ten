<template>
<section class="">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Consultants</h3>
                    <div class="card-tools">
                        <div class="input-group" style="width: 350px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="filters.query">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" @click="applyFilters"><i class="fas fa-search"></i></button>
                                <select class="form-control ml-1" v-model="filters.status" @change="getAllInitials">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height:500px;">
                    <CPDetailConsultantList :consultants.sync="consultants.data" :source="type" @refreshConsultantList="getAllInitials" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="consultants.per_page != null ? consultants.per_page : 52" :records="consultants.total != null ? consultants.total : 550" >
                    </pagination>
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
            consultants:   {data: [], total: 0,},
            current_page: 1,
            filters: {
                query: '',
                status: ''
            },
            showFilters: false,
            status: '',
            type: 'admin',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        applyFilters() {
            this.getAllInitials();
        },
        closeModal(){
            $('#Modal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/consultant_practices/consultants',  {
                params: {
                    page: this.current_page,
                    query: this.filters.query,
                    status: this.filters.status,
                    type: this.type,
                }
            })
            .then(response => {
                this.consultants = response.data.consultants ?? {data: [], total: 0,};
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Patients did not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
    },
    resetFilters() {
        this.filters = {
            keyword: '',
            patient: '',
            start_date: '',
            end_date: '',
            channel: ''
        };
        this.getAllInitials();
    },
}
</script>