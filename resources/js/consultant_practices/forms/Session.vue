<!--template>
<section class="overlay-wrapper p-0">
    <div class="container-fluid">
        <form @submit.prevent="editMode ? updateSession() : createSession()">
            <alert-error :form="sessionData"></alert-error> 
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Patient Type</label>
                        <select class="form-control" v-model="sessionData.patient_type" placeholder="Select Patient Type">
                            <option value="">--Patient Type--</option>
                            <option value="existing">Existing</option>
                            <option value="new">New</option> 
                        </select>
                    </div>
                </div>
                <div class="col-md-8" v-if="sessionData.patient_type == 'existing'">
                    <div class="form-group">
                        <label>Patient</label>
                        <select class="form-control" v-model="sessionData.patient_id" placeholder="Select Patient Type">
                            <option value="">--Select Patient--</option> 
                            <option v-for="patient in patients" :value="patient.id">{{ patient.name+' ['+patient.unique_id+']' }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4" v-if="sessionData.patient_type != 'existing'">
                    <div class="form-group">
                        <label>Patient Name</label>
                        <input type="text" class="form-control" placeholder="first name & last name" v-model="sessionData.patient.name" id="patient_name" name="patient_name" />
                    </div>
                </div>
                <div class="col-md-4" v-if="sessionData.patient_type != 'existing'">
                    <div class="form-group">
                        <label>Patient EMR ID</label>
                        <input type="text" placeholder="SNH-12345" class="form-control" v-model="sessionData.patient.name" id="patient_name" name="patient_name" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Specialty</label>
                        <select class="form-control" id="specialty_id" name="specialty_id" v-model="sessionData.specialty_id" required>
                            <option value="">--Select Specialty--</option>
                            <option v-for="specialty in specialties" :key="specialty.id" :value="specialty.id">{{specialty.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Consultant</label>
                        <select class="form-control" id="consultant_id" name="consultant_id" v-model="sessionData.consultant_id" required>
                            <option value="">--Select Consultant--</option>
                            <option v-for="consultant in filtered_consultant" :key="consultant.id" :value="consultant.id">{{consultant.first_name}} {{consultant.last_name}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Service</label>
                        <select class="form-control" id="specialty_id" name="specialty_id" v-model="sessionData.service_id" required>
                            <option value="">--Select Service--</option>
                            <option v-for="service in filtered_services" :key="service.id" :value="service.id">{{service.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                        <label>Date</label>
                        <input class="form-control" type="date" name="date" id="date" v-model="sessionData.date" @change="searchSchedule()"/>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                        <label>Session Payment Type</label>
                        <select class="form-control" name="payment_type" id="payment_type" v-model="sessionData.payment_type" required>
                            <option value="">--Select Payment Type--</option>
                            <option value="Cash">Cash</option>
                            <option value="Credit">Credit</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Amount</label>
                        <input class="form-control" id="amount" name="amount" v-model="sessionData.amount" required disabled/>
                    </div>
                </div>
            </div>
            <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
        </form>
    </div>
</section>
</template-->
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
                    <select v-model="sessionData.patient_id" class="form-control">
                        <option value="">--Select Patient--</option>
                        <option v-for="p in patients" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Consultant</label>
                    <select v-model="sessionData.consultant_id" class="form-control" @change="fetchConsultantServices">
                        <option v-for="c in consultants" :key="c.id" :value="c.id">{{ c.title }}. {{ c.first_name }} {{ c.last_name }}</option>
                    </select>
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
    emits:['refreshSession'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        codeAndNameAndDesc(item){
            return `${item.last_name}, ${item.first_name} ${item.middle_name}`
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
                this.$emit('refreshSession', response);
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
                this.$emit('refreshSession', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Session details has been modified',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });          
        },
    },
    props:{
        editMode: Boolean,
        session: Object,
    },
    watch:{
        session(){
            this.sessionData.fill(this.session);
        }
    }
}
</script>