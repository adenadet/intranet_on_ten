<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>

    <form @submit.prevent="editMode ? updatePayment() : createPayment()">
        <div class="row">
            <!-- Company -->
            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        Company
                    </label>

                    <select
                        class="form-control"
                        :class="{ 'is-invalid': paymentData.errors.has('company_id') }"
                        v-model="paymentData.company_id"
                        @change="onCompanyChange"
                    >
                        <option value="">
                            Select Company
                        </option>

                        <option
                            v-for="company in companies"
                            :key="company.id"
                            :value="company.id"
                        >
                            {{ company.name }}
                        </option>
                    </select>

                    <has-error
                        :form="paymentData"
                        field="company_id"
                    />
                </div>
            </div>

            <!-- Account -->
            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        Account
                    </label>

                    <select
                        class="form-control"
                        :class="{ 'is-invalid': paymentData.errors.has('account_id') }"
                        v-model="paymentData.account_id"
                    >
                        <option value="">
                            Select Account
                        </option>

                        <option
                            v-for="account in accounts"
                            :key="account.id"
                            :value="account.id"
                        >
                            {{ account.name }}
                        </option>
                    </select>

                    <has-error
                        :form="paymentData"
                        field="account_id"
                    />
                </div>
            </div>

            <!-- Amount -->
            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        Amount
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        class="form-control"
                        placeholder="Enter Amount"
                        :class="{ 'is-invalid': paymentData.errors.has('amount') }"
                        v-model="paymentData.amount"
                    >

                    <has-error
                        :form="paymentData"
                        field="amount"
                    />
                </div>
            </div>

            <!-- Date -->
            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        Payment Date
                    </label>

                    <input
                        type="date"
                        class="form-control"
                        :class="{ 'is-invalid': paymentData.errors.has('date') }"
                        v-model="paymentData.date"
                    >

                    <has-error
                        :form="paymentData"
                        field="date"
                    />
                </div>
            </div>

            <!-- Description -->
            <div class="col-md-12">
                <div class="form-group">
                    <label>
                        Description
                    </label>

                    <textarea
                        rows="4"
                        class="form-control"
                        placeholder="Enter Description"
                        :class="{ 'is-invalid': paymentData.errors.has('description') }"
                        v-model="paymentData.description"
                    ></textarea>

                    <has-error
                        :form="paymentData"
                        field="description"
                    />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <i class="fas fa-save"></i>{{ submitting ? 'Processing...' : (editMode ? 'Update Payment' : 'Create Payment') }}
                </button>
            </div>
        </div>
    </form>
</section>
</template>

<script>
export default {
    data() {
        return {
            accounts: [],
            companies: [],
            loading: false,
            paymentData: new Form({
                company_id: '',
                account_id: '',
                amount: '',
                description: '',
                date: ''
            })
        };
    },
    emits:['refreshPaymentForm'],
    methods: {
        createPayment(){
            this.loading = true;
            this.paymentData.post('/api/consultant_practices/payments')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'A new consultant was created successfully',
                });
                this.$emit('refreshPaymentForm', response);
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
        async getAllInitials() {
            this.loading = true;
            axios.get('/api/consultant_practices/payments/initials')
            .then(response =>{
                this.companies = response.data.companies || [];
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Payment form not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        onCompanyChange() {
            this.paymentData.account_id = '';
            const selectedCompany = this.companies.find(
                company => company.id == this.paymentData.company_id
            );
            this.accounts = selectedCompany ? (selectedCompany.accounts || []) : [];
        },
        resetForm() {
            this.paymentData.reset();
            this.paymentData.date = this.getTodayDate();
            this.accounts = [];
        },
        getTodayDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        },
        updatePayment(){
            this.loading = true;
            this.paymentData.put('/api/consultant_practices/payments/'+this.paymentData.id)
            .then(response =>{
                this.$emit('refreshPaymentForm', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Payment details has been modified',
                    showConfirmButton: false,
                    timer: 1500
                });
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
    props:{
        payment: {type: Object, default: null},
        editMode: Boolean,
    },
    watch:{
        payment(){
            if (this.payment != null){
                this.paymentData.fill(this.payment);
            }
            else{
                this.resetForm()
            }
        }
    }
};
</script>