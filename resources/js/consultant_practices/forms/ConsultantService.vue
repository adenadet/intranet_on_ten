<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form class="">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Consultant</label>
                    
                    <div class="form-control" v-if="consultant != null">{{ consultant.title }}. {{ consultant.first_name }} {{ consultant.last_name }}</div>
                    <model-list-select :list="consultants" v-else v-model="consultantServiceData.consultant_id" option-value="id" :custom-text="consultantName" placeholder="Select Consultant"/>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label>Service</label>
                    <model-list-select :list="services" v-model="consultantServiceData.service_id" option-value="id" option-text="name" placeholder="Select Service"/>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" class="form-control" v-model="consultantServiceData.price" placeholder="Price">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" v-model="consultantServiceData.status" placeholder="Status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <button class="btn btn-primary" type="button" @click="editMode ? updateService() : createService()">{{editMode ? 'Update' : 'Add'}} Service</button>
            </div>
        </div>
    </form>
</section>
</template>

<script>
import { ModelListSelect } from 'vue-search-select';

export default {
    components: {
        ModelListSelect
    },
    computed: {},
    data() {
        return {
            consultants: [],
            consultantServiceData: new Form({
                id: '',
                consultant_id: '',
                price: '',
                service_id: '',
                status: 1,
            }),
            loading: false,
            services: [],
        }
    },
    emits:['refreshConsultantServiceForm'],
    methods: {
        consultantName(item){
            return `${item.title}. ${item.first_name} ${item.last_name}`
        },
        createService(){
            this.loading = true;
            this.consultantServiceData.post('/api/consultant_practices/consultant_services')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'A new consultant service was created successfully',
                });
                this.$emit('refreshConsultantServiceForm', response);
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
        },
        async getInitials() {
            this.loading= true;
            axios.get('/api/consultant_practices/consultant_services/initials')
            .then(response =>{
                this.services = response.data.services;
                this.consultants = response.data.consultants;
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Consultant form not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        updateService(){
            this.loading = true;
            this.consultantServiceData.put('/api/consultant_practices/consultant_services/'+this.consultantServiceData.id)
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'The consultant service was updated successfully',
                });
                this.$emit('refreshConsultantServiceForm', response);
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
        },
    },

    mounted() {
        this.getInitials()
    },
    props: {
        editMode: { type: Boolean, required: false, default: false},
        consultant: { type: Object, required: false, default: null },
        consultant_service: { type: Object, required: false, default: () => [] }
    },
    watch: {
        consultant() {
            if (this.consultant != null){
                this.consultantServiceData.consultant_id = this.consultant.id;
            }
        },
        consultant_service() {
            this.consultantServiceData.fill(this.consultant_service);
            if (this.consultant != null){
                this.consultantServiceData.consultant_id = this.consultant.id;
            }
        }
    }
}
</script>