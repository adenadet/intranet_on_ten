<template>
<div class="card card-primary ">
    <div class="modal fade" id="termsModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy"><h4 class="modal-title">Terms and Conditions</h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true" class="text-white">&times;</span></button></div>
                <div class="modal-body">
                    <ExternalDetailPolicy />
                </div>
            </div>
        </div>
    </div>
    <div class="card-header bg-navy">Schedule An Appointment</div>
    <div class="card-body overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <alert-error :form="ApplicantData"></alert-error> 
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Service</label>
                        <select class="form-control" id="service_id" name="service_id" v-model="ApplicantData.service_id" required>
                            <option value="">--Select Service--</option>
                            <option v-for="service in services" :key="service.id" :value="service.id">{{service.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Appointment Date</label>
                        <input class="form-control" type="date" name="date" id="date" :min="tomorrow" v-model="ApplicantData.date" @change="searchSchedule()"/>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Schedule</label>
                        <select class="form-control" id="schedule" name="schedule" v-model="ApplicantData.schedule" required>
                            <option value=''>--Select Available Time--</option>
                            <option v-for="(schedule, index) in schedules" :key="index" :value="schedule.schedule">{{schedule.schedule}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" v-if="ApplicantData.service_id == 1">
                <div class="col-md-12 table-responsive p-o">
                    <table class="table table-striped table-hover">
                        <thead class="bg-dark">
                            <tr><th colspan="3">Next Available Slots</th></tr>
                            <tr><th>#</th><th>Date</th><th>Schedule</th></tr>
                        </thead>
                        <tbody v-if="uk_tb_slots.length != 0">
                            <tr v-for="(schedule, index) in uk_tb_slots">
                                <td>{{ addOne(index) }}</td>
                                <td>{{ schedule.date }}</td>
                                <td>{{ schedule.schedule }}</td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr><td colspan="5">No Schedule available for selected date</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Last Name*</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name *" required v-model="ApplicantData.last_name" :class="{'is-invalid' : ApplicantData.errors.has('last_name') }" />
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" required class="form-control" id="first_name" name="first_name" placeholder="First Name *" v-model="ApplicantData.first_name" :class="{'is-invalid' : ApplicantData.errors.has('first_name') }">
                        <has-error :form="ApplicantData" field="first_name"></has-error> 
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="middle Name" v-model="ApplicantData.middle_name" :class="{'is-invalid' : ApplicantData.errors.has('middle_name') }"/>
                        <has-error :form="ApplicantData" field="middle_name"></has-error> 
                    </div>
                </div>  
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <label>Date of Birth *</label>
                    <div class="form-group">
                        <input name="dob" id="dob" type="date" :max="today" data-provide="datepicker" data-date-autoclose="true" class="form-control" required placeholder="Birth Date" v-model="ApplicantData.dob" :class="{'is-invalid' : ApplicantData.errors.has('dob') }" @change="updateAmount()" >
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Sex *</label>
                        <select class="form-control" id="sex" name="sex" required v-model="ApplicantData.sex" :class="{'is-invalid' : ApplicantData.errors.has('sex') }">
                            <option value=''>---Select Sex---</option>
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Last Menstrual Period (Females only)</label>
                        <input type="date" class="form-control" id="lmp" name="lmp" placeholder="Enter Last Menstrual Period *" v-model="ApplicantData.lmp" :class="{'is-invalid' : ApplicantData.errors.has('lmp') }" v-show="ApplicantData.sex == 'Female'"/>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Nationality</label>
                        <select class="form-control" id="nationality_id" name="nationality_id" v-model="ApplicantData.nationality_id" :class="{'is-invalid' : ApplicantData.errors.has('nationality_id') }">
                            <option value=''>---Select Nationality---</option>
                            <option v-for="nation in nations" v-bind:key="nation.id" :value="nation.id" >{{nation.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Passport Number</label>
                        <input type="text" class="form-control" id="passport_no" name="passport_no" placeholder="Enter Passport Number *" required v-model="ApplicantData.passport_no" :class="{'is-invalid' : ApplicantData.errors.has('passport_number') }" />
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Visa Type</label>
                        <input type="text" class="form-control" id="visa_type" name="visa_type" placeholder="Enter Visa Type *" required v-model="ApplicantData.visa_type" :class="{'is-invalid' : ApplicantData.errors.has('visa_type') }" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number *" required v-model="ApplicantData.phone" :class="{'is-invalid' : ApplicantData.errors.has('phone') }">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address *" required v-model="ApplicantData.email" :class="{'is-invalid' : ApplicantData.errors.has('email') }">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                        <label>Address in Nigeria*</label>
                        <input type="text" v-model="ApplicantData.nigerian_address_street" class="form-control" id="nigerian_address_street" name="nigerian_address_street" placeholder="Street Address e.g. 2, Olabode Street"/>
                        <input type="text" v-model="ApplicantData.nigerian_address_street2" class="form-control" id="nigerian_address_street2" name="nigerian_address_street2" placeholder="Street Address e.g. off Station Road"/>
                        <input type="text" v-model="ApplicantData.nigerian_address_city" class="form-control" id="nigerian_address_city" name="nigerian_address_city" placeholder="Town or City e.g. Ondo,"/>
                        <input type="text" v-model="ApplicantData.nigerian_address_country" class="form-control" id="nigerian_address_country" name="nigerian_address_country" placeholder="Country e.g. Nigeria"/>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                        <label>Address in the UK*</label>
                        <input type="text" v-model="ApplicantData.uk_address_street" class="form-control" id="uk_address_street" name="uk_address_street" placeholder="Street Address e.g. University of Birmingham"/>
                        <input type="text" v-model="ApplicantData.uk_address_street2" class="form-control" id="uk_address_street2" name="uk_address_street2" placeholder="Street Address e.g. Edgbaston"/>
                        <input type="text" v-model="ApplicantData.uk_address_city" class="form-control" id="uk_address_city" name="uk_address_city" placeholder="Town or City e.g. Birmingham"/>
                        <input type="text" v-model="ApplicantData.uk_address_postcode" class="form-control" id="uk_address_postcode" name="uk_address_postcode" placeholder="Postcode e.g. B15 2TT"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="terms" id="terms" v-model="terms">
                        <label class="form-check-label">I have read and accepted the <a href="#" @click.prevent="viewTerms()">Terms and conditions</a> of St. Nicholas Hospital as well as the United Kingdom Home office.</label>
                    </div>
                </div>
            </div>
            <!--NairafyButton :amount="ApplicantData.amount" :phone="(ApplicantData.phone).toString()" :first_name="ApplicantData.first_name" :last_name="ApplicantData.last_name" :vendor_id="vendor_id" :unique_id="nairafyReference" :email="ApplicantData.email"  
            :beforePay="saveData"
            :onSuccess="response => processAppointment('nairafy', response)" 
            :onFail="nairafyErrorAppointment" /-->          
            <button class="btn btn-primary ml-3" :disabled="isButtonDisabled" @click="handleCreateThenPay">PAY NGN {{ ApplicantData.amount }} with Paystack</button>
            <paystack ref="paystackRef" buttonClass="d-none" :publicKey="PUBLIC_KEY" :email="ApplicantData.email" :amount="ApplicantData.amount * 100" :reference="paystackReference" :onSuccess="handlePaystackSuccess" :onCancel="handlePaystackCancel"/>
    </div>
    <div class="card-footer">
        Kindly note the terms 
    </div>
</div>
</template>
<script>
import paystack from 'vue3-paystack';
import NairafyButton from '../../plugins/nairafy-button.vue';
export default {
    components: {
        paystack
    },
    computed: {
        isButtonDisabled() {
            const a = this.ApplicantData;
            return (
                this.loading ||
                !this.terms ||
                !a.email ||
                !a.first_name ||
                !a.last_name ||
                !a.schedule
            );
        },
    },
    data(){
        return  {
            ApplicantData: new Form({
                first_name: '', 
                middle_name:'', 
                last_name:'', 
                amount: 0,
                dob: '',
                sex:'',
                lmp:'', 
                nationality_id: '',
                alt_phone:'', 
                phone:'', 
                email:'',
                id:'', 
                image:'', 
                nigerian_address:'', 
                nigerian_address_street:'',
                nigerian_address_street2:'', 
                nigerian_address_city:'',
                nigerian_address_country:'',   
                uk_address:'',
                uk_address_street:'',
                uk_address_street2:'',
                uk_address_city:'',
                uk_address_postcode:'',
                accompanying_kids: 0,
                visa_type: '',
                passport_number: '',
                schedule: '',
                service_id: '',
                date: '',
                payment_method:'',
                payment_reference: '',
                payment_transaction: '',
            }),
            today: '',
            tomorrow: '',
            PUBLIC_KEY: "pk_live_9e3c92567f7ad310ae7c28e248b8edb67ca2661a",
            PUBLIC_TEST: "pk_test_a598743a2527b186e293b76fb39bcfa6834eb153",
            loading: false,
            nairafyReference: this.genRef('nairafy'),
            nations: [],
            paystackReference: this.genRef('paystack'),
            schedules: [],
            serverTxnId: null,
            services: [], 
            terms: false,
            uk_tb_slots: [],
            vendor_id: "47c3ac1b-361c-488e-b8bb-0c56da0411df", 
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        closeModal(){
            $('#termsModal').modal('hide');
        },
        createApplicant(){
            this.loading = true;
            this.ApplicantData.post('/api/scheduler')
            .then(response =>{
                this.loading = false;
                this.ApplicantData.reset();
                this.$swal.fire({icon: 'success', title: 'The Profile details has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });  
        },
        genRef(type) {
            const prefixMap = {
                nairafy: 'Nairafy_',
                paystack: 'Paystack_',
            }
            const ref = (prefixMap[type] || 'TRX_') + new Date().valueOf()

            if (type === 'nairafy') this.reference_nairafy = ref
            else if (type === 'paystack') this.reference_paystack = ref
            
            return ref
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/scheduler')
            .then(response => {;
                var today = new Date();
                var dd = today.getDate();
                var dt = dd + 1;
                var mm = today.getMonth()+1;
                 
                var yyyy = today.getFullYear();
                if(dd<10){dd='0'+dd;} 
                if(dt<10){dt='0'+dt;} 
                if(mm<10){mm='0'+mm;} 
                today = yyyy+'-'+mm+'-'+dd;
                var tomorrow = yyyy+'-'+mm+'-'+dt;
            this.today = today;
            this.tomorrow = tomorrow
            this.refreshScheduler(response)
            this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        async handlePaystackCancel() {
            axios.delete(`/api/scheduler/${this.serverTxnId}`)  
        },
        async handleCreateThenPay() {
            try {
                this.loading = true;
                await this.ApplicantData.post('/api/scheduler')
                .then(response =>{
                    this.loading = false;
                    this.serverTxnId = response.data.appointment.id;
                    this.$refs.paystackRef.payWithPaystack();
                })
                .catch(()=>{
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        footer: 'Please try again later!'
                    });
                    this.loading = false;
                });  
            } 
            catch (err) {
                if (err.response?.data?.errors) {
                    this.ApplicantData.errors.record(err.response.data.errors);
                }
            } 
            finally {this.loading = false;}
        },
        async handlePaystackSuccess(ps) {
            try {
                this.loading = true;
                await axios.put(`/api/scheduler/${this.serverTxnId}`, {
                    payment_reference : ps.reference,
                    payment_transaction: ps.transaction || ps.reference,
                    payment_method : 'Paystack',
                    reference       : ps.reference,
                    status          : ps.status,
                    raw_response    : ps,          // optionally persist full payload
                })
                .then(()=>{
                    this.$swal.fire({icon: 'success', title: 'The Appointment details has been created', showConfirmButton: false, timer: 1500});
                })
                .catch(()=>{
                    this.$swal.fire({icon: 'error', title: 'Something went wrong, check if you got a payment receipt in your email', showConfirmButton: false, timer: 1500});
                })
                .finally(()=>{
                    this.ApplicantData.reset();
                    this.loading = false;
                });
            } 
            catch (err) {
                this.$swal.fire({icon: 'error', title: 'Something went wrong, check if you got a payment receipt in your email', showConfirmButton: false, timer: 1500});
                this.loading = false;
            }
        },
        isWeekend(date){
            var cast = new Date(date)
            return cast.getDay() === 6 || cast.getDay() === 0;
        },
        async nairafyErrorAppointment(response){
            if (response.message != "Approved"){
                alert("Payment was unsuccessful");
                this.ApplicantData.payment_method = "Nairafy";
                this.ApplicantData.payment_reference= response.reference;
                this.ApplicantData.payment_transaction = response.transaction;
            }
            else{
                alert("Payment has to be made to confirm booking");
            }
        },
        async nairafyProcessAppointment(response){
            try {
                await axios.put(`/api/scheduler/${this.serverTxnId}`, {
                    payment_gateway : 'nairafy',
                    reference       : ps.unique_id,
                    status          : ps.status,
                    raw_response    : ps,          // optionally persist full payload
                });

                this.ApplicantData.reset();
                this.$swal.fire({icon: 'success', title: 'The Profile details has been created', showConfirmButton: false, timer: 1500});
            } 
            catch (err) {
                // If this fails you may want to flag the appointment for review
            }
        },
        refreshScheduler(response){
            this.services = response.data.services;
            this.nations = response.data.nations;
            this.uk_tb_slots = response.data.uk_tb_slots;
        },
        processAppointment(channel, response){
            this.ApplicantData.payment_method = channel;
            if (channel == 'nairafy'){
                this.ApplicantData.payment_transaction = response.transaction.unique_code;
                this.ApplicantData.payment_reference = response.transaction.payment ? response.transaction.payment.description : '';    
            }
            else{
                this.ApplicantData.payment_reference = response.reference;
                this.ApplicantData.payment_transaction = response.transaction;
            }
            this.createApplicant();
        },
        processErrorAppointment(channel, response){
            if (response.message != "Approved"){
                this.ApplicantData.payment_method = "Paystack";
                this.ApplicantData.payment_reference= response.reference;
                this.ApplicantData.payment_transaction = response.transaction;

                //this.createApplicant();
            }
            else{
                alert("Payment has to be made to confirm booking");
            }
        },
        processBooking(){
            this.ApplicantData.payment_method = "Holding";
        },
        saveData() {
            this.ApplicantData.reference_id = (channel == 'nairafy') ? this.nairafyReference : this.paystackReference;
            this.loading = true
            this.ApplicantData.post('/api/scheduler')
            .then((response) => {
                this.serverTxnId = response.data.appointment.id;
            })
            .catch(err => {
                this.$toast.fire({ icon: 'error', title: 'Could not save appointment' })
                return Promise.reject(err)
            })
            .finally(() => { this.loading = false })
        },
        searchSchedule(){
            if (this.ApplicantData.service_id == ""){
                alert("Please select the service type");
                this.ApplicantData.date = "";
                return;
            }
            else if (this.isWeekend(this.ApplicantData.date)){
                alert("Weekend not available for selection");
                this.ApplicantData.date = "";
                return;
            }
            axios.get('/api/schedules?service_id='+this.ApplicantData.service_id+'&date='+this.ApplicantData.date)
            .then(response =>{
                if (response.data.schedules.length == 0){
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'There is no space for this date!',
                        footer: 'Please choose a later date!'
                    });
                }
                this.schedules = response.data.schedules;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({ icon: 'error', title: 'Schedules not loaded successfully',})
            });
        },
        updateAmount(){
            var dob = new Date(this.ApplicantData.dob);
            var month_diff = Date.now() - dob.getTime();  
            var age_dt = new Date(month_diff);   
            var year = age_dt.getUTCFullYear();  
            var age = Math.abs(year - 1970);  
            
            if (age >= 11){this.ApplicantData.amount = 120000;}
            else {this.ApplicantData.amount = 60000;}
        },
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.ApplicantData.image = reader.result
                }
                reader.readAsDataURL(file)
            }
            else{
                this.$swal.fire({type: 'error', title: 'File is too large'})
            }
        },
        viewTerms(){
            $('#termsModal').modal('show');
        }
    },
}
</script>