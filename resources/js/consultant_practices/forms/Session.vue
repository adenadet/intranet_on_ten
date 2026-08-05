<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="submit">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" v-model="sessionData.date" class="form-control"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Patient</label>
                    <model-list-select class="form-control" :list="patients" v-model="sessionData.patient_id" option-value="id" optiontext="name" placeholder="Select Patient" />
                    <!--select v-model="sessionData.patient_id" class="form-control">
                        <option value="">--Select Patient--</option>
                        <option v-for="p in patients" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select-->
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Consultant</label>
                    <model-list-select class="form-control" :list="consultants" v-model="sessionData.consultant_id" option-value="id" :custom-text="codeAndNameAndDesc" placeholder="Select Applicant" />
                </div>        
            </div>
            <div class="col-12">
                <label>Services</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 form-check" v-for="service in consultantServices" :key="service.id">
                <input type="checkbox" :value="service" v-model="selectedServices" class="form-check-input"/>
                <label class="form-check-label">{{ service.service?.name }} - ₦{{ service.price }}</label>
            </div>
        </div>
        <div v-if="selectedServices.length">
            <h5>Selected Services</h5>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Original</th>
                        <th>Adjusted</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="s in computedServices" :key="s.id">
                        <td>{{ s.service?.name }}</td>
                        <td>{{ currency(s.price) }}</td>
                        <td>{{ currency(s.adjusted_price) }} 
                            <span v-if="consultantPaymentType === 'halving'" class="badge bg-warning float-right">Halving Rule Applied</span>                 
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-md-6">&nbsp;</div>
            <div class="col-md-6">
                <strong>Total: <span class="float-right">{{ currency(totalPrice) }}</span></strong>
            </div>
        </div>
        <button class="btn btn-primary" type="button" @click.prevent="editMode ? updateSession() : createSession()">Save Session</button>
    </form>
</section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';
export default {
    components: {ModelListSelect},
    computed:{
        computedServices() {
            if (!this.selectedServices.length) return []
            // clone to avoid mutation
            let services = JSON.parse(JSON.stringify(this.selectedServices))
            if (this.consultantPaymentType === 'halving') {
                services.sort((a, b) => b.price - a.price)
                return services.map((s, index) => { return {...s, adjusted_price: index === 0 ? s.price : s.price / 2}})
            }

            return services.map(s => ({...s, adjusted_price: s.price}))
        },
        filtered_consultants(){
            if (!this.sessionData.specialty_id) {return this.consultants;}

            return this.consultants.filter(consultant => consultant.specialty_id === this.sessionData.specialty_id);
        },
        filtered_services(){
            if (!this.sessionData.consultant_id) {return this.services;}
            const consultant = this.consultants.find(c => c.id === this.sessionData.consultant_id);
            return consultant ? consultant.services : this.services;
        },
        totalPrice() {
            return this.computedServices.reduce(
                (sum, s) => sum + Number(s.adjusted_price),
                0
            )
        },
    },
    data(){
        return  {
            consultants: [],
            consultantPaymentType: null,
            consultantServices: [],
            loading: false,
            patients: [],
            selectedServices: [],                                                                                                               
            services: [],
            sessionData: new Form({
                specialty_id: "",
                consultant_id: "",
                patient_id: "",
                services: [],
                date: "",
                id: "",
            }),
            specialties: [],
        }
    },
    emits:['refreshSessionForm'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        codeAndNameAndDesc(item){
            return `Dr. ${item.first_name} ${item.last_name}`
        },
        createSession(){
            this.loading = true;
            this.sessionData.services = this.computedServices;
            this.sessionData.amount = this.totalPrice;
            this.sessionData.post('/api/consultant_practices/sessions')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'A new session was created successfully',
                });
                this.$emit('refreshSessionForm', response);
            })
            .catch(error=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error?.response?.data?.message || 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            })
            .finally(()=>{
                this.loading = false;
            });   
        },
        fetchConsultantServices() {
            this.loading= true;
            this.selectedServices = [];
            axios.get('/api/consultant_practices/consultant_services/consultant/'+this.sessionData.consultant_id)
            .then(response =>{
                this.consultantServices = response.data.consultant_services
                this.consultantPaymentType = response.data.consultant_payment_type
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Session form not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });    
        },
        getAllInitials(){
            this.loading= true;
            axios.get('/api/consultant_practices/sessions/initials')
            .then(response =>{
                this.consultants = response.data.consultants;
                this.specialties = response.data.specialties;
                this.services = response.data.services;
                this.patients = response.data.patients;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Session form not loaded successfully',
                })
            });
        },
        updateSession(){
            this.loading = true;
            this.sessionData.services = this.computedServices;
            this.sessionData.amount = this.totalPrice;
            this.sessionData.put('/api/consultant_practices/sessions/'+this.sessionData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('refreshSessionForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Session details has been modified',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(error=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error?.response?.data?.message || 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            })
            .finally(()=>{
                this.loading = false;
            });          
        },
    },
    props:{
        editMode: Boolean,
        session: Object,
    },
    watch:{
        'sessionData.consultant_id'(consultantId) {
            this.fetchConsultantServices(consultantId);
        },
        session(){
            this.sessionData.fill(this.session);
        }
    }
}
</script>