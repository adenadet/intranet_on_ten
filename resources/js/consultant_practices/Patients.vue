<template>
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title"><i class="fa fa-user-injured"></i> Patient List</h3>
                <div class="card-tools">
                    <div class="input-group input-group" style="width: 550px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary mr-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                            <select class="form-control mr-1" v-model="status" @change="getAllInitials">
                                <option value="0">Inactive</option>
                                <option value="1">Active</option>
                                <option value="all">All</option>
                            </select>
                            <button type="button" class="btn btn-primary ml-1" @click="uploadEmployees"><i class="fa fa-upload"></i></button>
                        </div>
                    </div>    
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 500px;">
                <CPDetailPatientList :patients="patients.data" :source="type" @refreshPatientList="getAllInitials" />
            </div>
            <div class="card-footer">
                <div class="col-md-12">
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
            query: '',
            patients:   {data: [], total: 0,},
            start_date: '',
            status: '',
            type: 'front',
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
            axios.get('/api/consultant_practices/patients?end_date='+this.end_date+'query='+this.query+'&start_date='+this.start_date+'&type='+this.type)
            .then(response => {
                this.patients = response.data.patients;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        
    },
}
</script>