
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
        <!--form class="" method="POST"-->
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
                        <QuillEditor contentType="html" rows="5" id="nigerian_address" name="nigerian_address" placeholder="Enter Address *" required v-model:content="ApplicantData.nigerian_address" :class="{'is-invalid' : ApplicantData.errors.has('nigerian_address') }"></QuillEditor>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                        <label>Address in the UK*</label>
                        <QuillEditor contentType="html" rows="5" id="uk_address" name="uk_address" placeholder="Enter Address *" required v-model:content="ApplicantData.uk_address" :class="{'is-invalid' : ApplicantData.errors.has('uk_address') }"></QuillEditor>
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
            <NairafyButton :amount="ApplicantData.amount" :phone="(ApplicantData.phone).toString()" :first_name="ApplicantData.first_name" :last_name="ApplicantData.last_name" :vendor_id="vendor_id" :unique_id="nairafyReference" :email="ApplicantData.email"  
            :beforePay="saveData"
            :onSuccess="response => processAppointment('nairafy', response)" 
            :onFail="nairafyErrorAppointment" />          
            <button class="btn btn-primary ml-3" :disabled="isButtonDisabled" @click="handleCreateThenPay">PAY NGN {{ ApplicantData.amount }} with Paystack</button>
            <paystack ref="paystackRef" buttonClass="d-none" :publicKey="PUBLIC_KEY" :email="ApplicantData.email" :amount="ApplicantData.amount * 100" :reference="paystackReference" :onSuccess="handlePaystackSuccess" :onCancel="handlePaystackCancel"/>
                <!--/form-->
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
        paystack,
        NairafyButton
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
            !a.schedule ||
            !a.amount ||
            !a.service_id
        );
        },
    },
    data() {
        return {
        ApplicantData: new Form({
            first_name: '',
            middle_name: '',
            last_name: '',
            amount: 0,
            dob: '',
            sex: '',
            lmp: '',
            nationality_id: '',
            alt_phone: '',
            phone: '',
            email: '',
            id: '',
            image: '',
            nigerian_address: '',
            uk_address: '',
            accompanying_kids: 0,
            visa_type: '',
            passport_number: '',
            schedule: '',
            service_id: '',
            date: '',
            payment_method: '',
            payment_reference: '',
            payment_transaction: '',
        }),
        today: '',
        tomorrow: '',
        PUBLIC_KEY: import.meta.env.VITE_PAYSTACK_PUBLIC_KEY,
        loading: false,
        reference_nairafy: '',
        reference_paystack: '',
        nations: [],
        schedules: [],
        serverTxnId: null,
        services: [],
        terms: false,
        vendor_id: import.meta.env.VITE_VENDOR_ID,
        };
    },
    mounted() {
        this.getInitials();
        this.reference_nairafy = this.genRef('nairafy');
        this.reference_paystack = this.genRef('paystack');
    },
    methods: {
        closeModal() {
            $('#termsModal').modal('hide');
        },
        async createApplicant() {
            try {
                this.loading = true;
                const response = await this.ApplicantData.post('/api/scheduler');
                this.loading = false;
                this.ApplicantData.reset();
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been created',
                    showConfirmButton: false,
                    timer: 1500,
                });
            } 
            catch (error) {
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops... Something went wrong!',
                    text: error.response?.data?.message || 'Please try again later!'
                });
            }
        },
        genRef(type) {
            const prefixMap = {
                nairafy: 'Nairafy_',
                paystack: 'Paystack_',
            };
            return (prefixMap[type] || 'Transaction_') + new Date().valueOf();
        },
        async getInitials() {
            try {
                this.loading = true;
                const response = await axios.get('/api/scheduler');
                const today = new Date();
                const dd = String(today.getDate()).padStart(2, '0');
                const dt = String(today.getDate() + 1).padStart(2, '0');
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const yyyy = today.getFullYear();
                this.today = `${yyyy}-${mm}-${dd}`;
                this.tomorrow = `${yyyy}-${mm}-${dt}`;
                this.refreshScheduler(response);
            } 
            catch (error) {
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not load successfully',
                });
            } 
            finally {
                this.loading = false;
            }
        },
        async handleCreateThenPay() {
        try {
            if (this.isButtonDisabled) return;
            this.loading = true;
            const response = await this.ApplicantData.post('/api/scheduler');
            this.serverTxnId = response.data.appointment.id;
            this.$refs.paystackRef.payWithPaystack();
        } 
        catch (error) {
            this.$swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.response?.data?.message || 'Please try again later!'
            });
        } 
        finally {
            this.loading = false;
        }
        },
        async handlePaystackCancel() {
            if (this.serverTxnId) {
                await axios.delete(`/api/scheduler/${this.serverTxnId}`);
            }
        },
        async handlePaymentSuccess(ref, gateway = 'paystack') {
            try {
                const payload = {
                    payment_method: gateway,
                    payment_reference: ref.reference,
                    payment_transaction: ref.transaction,
                };
                await axios.put(`/api/scheduler/${this.serverTxnId}`, payload);
                this.$swal.fire({
                    icon: 'success',
                    title: 'Payment successful!',
                    showConfirmButton: false,
                    timer: 2000,
                });
            } 
            catch (error) {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Payment update failed',
                    text: 'Please contact support with your reference number.',
                });
            }
        },
    },
};
</script>