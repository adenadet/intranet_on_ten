<template>
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title">Sessions</h3>
                <div class="card-tools">
                    <div class="input-group" style="width: 650px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default mr-1"><i class="fas fa-search"></i></button>
                            <select class="form-control mr-1" v-model="type">
                                <option value="">--Select Status--</option>
                                <option value="created">Created</option>
                                <option value="deleted">Deleted</option>
                            </select>
                            <input type="date" class="form-control mr-1" v-model="start_date" />
                            <input type="date" class="form-control mr-1" v-model="end_date" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 500px;">
                <CPDetailSessionList :sessions="sessions.data" source="front_office" @refreshSessionList="refreshPage"/>
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="sessions.per_page != null ? sessions.per_page : 52" :records="sessions.total != null ? sessions.total : 550" ></pagination>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            end_date: '',
            query: '',
            sessions:   {data: [], total: 0,},
            start_date: '',
            type: '',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        closeModal(){
            $('#termsModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/consultant_practices/sessions?end_date='+this.end_date+'query='+this.query+'&start_date='+this.start_date+'&type='+this.type)
            .then(response => {;
                this.consultants = response.data.consultants;
                this.patients = response.data.patients;
                this.payments = response.data.payments;
                this.consultants = response.data.consultants;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        
    },
}
</script>