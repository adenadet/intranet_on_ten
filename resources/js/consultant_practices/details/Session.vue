<template>
<section class="overlay-wrapper p-0">
    <div class="invoice p-3 mb-3">
        <div class="row">
            <div class="col-12">
                <h4>
                    <i class="fas fa-globe"></i> AdminLTE, Inc.
                    <small class="float-right">Date: {{ ExcelDate(session.date) }}</small>
                </h4>
            </div>
        </div>
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                Patient:
                <address>
                    <strong>{{ session.patient?.name }}</strong><br>
                    {{ session.patient?.unique_id }}<br>
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                Consultant:
                <address>
                    <strong>{{ session.consultant?.title+'. '+session.consultant?.first_name+' '+session.consultant?.last_name }}</strong><br>
                    {{ session.consultant?.company?.name }}<br>
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                <b>Session ID: {{ session.unique_id }}</b><br>
                
                <b>Created By:</b> {{FullName(session.creator)}}<br>
                <b>Date:</b> {{ ExcelDate(session.created_at) }}<br>
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Service/Procedure</th>
                            <th>Original Price</th>
                            <th>Adjusted Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in session.session_items" :key="item.id">
                            <td>{{ addOne(index) }}</td>
                            <td>{{ item.service?.name }}</td>
                            <td>{{ currency(item.price) }}</td>
                            <td>{{ currency(item.adjusted_price) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    This is totally confidential information intended only for the consultant and patient involved in this session. Any unauthorized review, use, disclosure, or distribution is prohibited. If you are not the intended recipient, please contact the sender and destroy all copies of this information.
                </p>
            </div>
            <div class="col-6">
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <td style="width:50%">Total:</td>
                            <td><strong>{{ currency(session.amount) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
}
</script>