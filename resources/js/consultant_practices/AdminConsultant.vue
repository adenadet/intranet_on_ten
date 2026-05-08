<template>
<section class="row">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="consultantServiceMultiFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Manage Consultant Service</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <CPFormConsultantServiceMultiple :editMode="editMode" :consultant.sync="consultant" :consultant_services.sync="services" @refreshConsultantServiceMultipleForm="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <CPDetailConsultant :consultant.sync="consultant" @refreshConsultantDetail="getAllInitials()" />
        <CPDetailCompany class="mt-3" :company.sync="consultant.company" @refreshCompanyDetail="getAllInitials()" />
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Services</h3>
                <div class="card-tools">
                    <button type="submit" class="btn btn-warning btn-sm" @click="editMultiServices"><i class="fas fa-edit"></i></button>    
                    <!--div class="input-group" style="width: 450px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default mr-1"><i class="fas fa-search"></i></button>
                            <select class="form-control" v-model="status">
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></button>
                        </div>
                    </div-->
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 600px;">
                <CPDetailConsultantServiceList :consultant_services.sync="services" :consultant.sync="consultant" source="consultant" @refreshConsultantServiceList="getAllInitials()" />
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            consultant: {company:{}, services: [],},
            loading: false,
            query: '',
            status: '',
            services: [],
            type: 'admin',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        closeModal(){
            $('#consultantServiceMultiFormModal').modal('hide');
        },
        editMultiServices(){
            this.loading = true;
            $('#consultantServiceMultiFormModal').modal('show');
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/consultant_practices/consultants/'+this.$route.params.id)
            .then(response => {
                this.consultant = response.data.consultant ?? {services: [], company: {},};
                this.services = response.data.services ?? [];
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Consultant did not load successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
    },
}
</script>