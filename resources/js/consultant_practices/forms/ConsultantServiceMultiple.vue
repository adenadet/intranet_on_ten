<template>
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Consultant Services & Pricing</h3>
        </div>

        <div class="card-body">

            <!-- Add New Row -->
            <div class="row mb-3">

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Select Service</label>
                        <model-list-select :list="availableServices" v-model="selectedService" option-value="id" option-text="name" placeholder="Select Service" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" v-model="newPrice" placeholder="Price">
                    </div>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary btn-block" @click="addService">Add Service</button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive" style="height: 250px;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th width="200">Price</th>
                            <th width="100"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in consultantServiceData.services" :key="index">
                            <td>{{ getServiceName(item.service_id) }}</td>
                            <td><input type="number" class="form-control" step="0.01" v-model="item.price"></td>
                            <td><button class="btn btn-danger btn-sm" @click="removeService(index)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                        <tr v-if="consultantServiceData.services.length === 0">
                            <td colspan="3" class="text-center text-muted">
                                No services added
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer text-right">
            <button class="btn btn-success" @click="submit">Save Services</button>
        </div>
    </div>
</section>
</template>

<script>
import { Form } from 'vform'
import { ModelListSelect } from 'vue-search-select'

export default {
    components: {
        ModelListSelect
    },
    data() {
        return {
            consultantServiceData: new Form({
                consultant_id: null,
                services: []
            }),
            services: [], // all services from API
            selectedService: null,
            newPrice: null,
        }
    },

    computed: {
        availableServices() {
            const selectedIds = this.consultantServiceData.services.map(s => s.service_id)
            return this.services.filter(s => !selectedIds.includes(s.id))
        }
    },

    methods: {
        addService() {
            if (!this.selectedService || !this.newPrice) return
            this.consultantServiceData.services.push({
                service_id: this.selectedService,
                price: this.newPrice
            })
            this.selectedService = null
            this.newPrice = null
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
        getServiceName(id) {
            const service = this.services.find(s => s.id === id)
            return service ? service.name : 'Unknown'
        },
        removeService(index) {
            this.consultantServiceData.services.splice(index, 1)
        },

        async submit() {
            this.loading = true;
            this.consultantServiceData.post('/api/consultant_practices/consultant_services/multiple')
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
        }
    },

    mounted() {
        this.getInitials()
        this.consultantServiceData.consultant_id = this.consultant.id
        this.consultantServiceData.services = this.consultant_services.map(s => ({
            service_id: s.service_id,
            price: s.price
        }))
    },
    props: {
        consultant: { type: Object, required: true },
        consultant_services: { type: Array, default: () => [] }
    },
    watch: {
        consultant() {
            if (this.consultant != null){this.consultantServiceData.consultant_id = this.consultant.id;}
        },
        consultant_services() {
            this.consultantServiceData.services = this.consultant_services.map(s => ({
                service_id: s.service_id,
                price: s.price
            }));

            if (this.consultant != null){
                this.consultantServiceData.consultant_id = this.consultant.id;
            }
        }
    }

}
</script>