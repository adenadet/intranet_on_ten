<template>
<section class="">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Patients</h3>
                    <div class="card-tools">
                        <div class="input-group" style="width: 450px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="filters.query">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default" @click="getAllInitials"><i class="fas fa-search"></i></button>
                                <select class="form-control ml-1" v-model="filters.status" @change="getAllInitials">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <button type="button" class="btn btn-tool ml-1" @click="showFilters = !showFilters"><i class="fas fa-filter text-dark"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="showFilters" class="card-body border-bottom bg-light">
                    <form @submit.prevent="applyFilters">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Unique ID</label>
                                    <input type="text" v-model="filters.unique_id" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Sex</label>
                                    <select v-model="filters.sex" class="form-control">
                                        <option value="">All</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Patient Type</label>
                                    <select v-model="filters.patient_type" class="form-control">
                                        <option value="">All</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Credit">Credit</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">Apply</button>
                                <button type="button" @click="resetFilters" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body table-responsive p-0" style="height:500px;">
                    <CPDetailPatientList :patients.sync="patients.data" :source="type" @refreshPatientList="getAllInitials" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="patients.per_page != null ? patients.per_page : 52" :records="patients.total != null ? patients.total : 550" ></pagination>
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
            filters: {
                patient_type: '',
                query: '',
                sex: '',
                status: '',
                unique_id: '',
            },
            patients:   {data: [], total: 0,},
            showFilters: false,
            start_date: '',
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
            axios.get('/api/consultant_practices/patients', {
                params: {
                    page: this.current_page,
                    query: this.filters.query,
                    sex: this.filters.sex,
                    status: this.filters.status,
                    unique_id: this.filters.unique_id,
                    type: this.type,
                }
            })
            .then(response => {
                this.patients = response.data.patients ?? {data: [], total: 0,};
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
            patient_type: '',
            query: '',
            sex: '', 
            status: '',
            unique_id: '',
        };
        this.getAllInitials();
    },
}
</script>