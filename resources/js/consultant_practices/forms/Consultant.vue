<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" class="form-control" id="first_name" v-model="consultantData.first_name" placeholder="Enter consultant name">
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" class="form-control" id="last_name" v-model="consultantData.last_name" placeholder="Enter unique ID">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" class="form-control" id="title" v-model="consultantData.title" placeholder="Enter unique ID">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Company Name</label>
                    <select  v-if="company == null" class="form-control" id="company_id" name="company_id" v-model="consultantData.company_id">
                        <option value="">Select Company</option>
                        <option v-for="c in companies" :value="c.id" :key="c.id">{{ c.name }}</option>
                        <option value="new">New Company</option>
                    </select>
                    <div v-else class="form-control">{{company.name}}</div>
                    <input type="text" class="form-control mt-2" id="new_company_name" v-if="consultantData.company_id === 'new'" placeholder="Enter new company name" v-model="consultantData.new_company_name">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="email" name="email" v-model="consultantData.email">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" v-model="consultantData.phone">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Billing Type</label>
                    <select class="form-control" id="billing_type" name="billing_type" v-model="consultantData.billing_type">
                        <option value="halving">Halving Method</option>
                        <option value="full">Always Full</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Specialty</label>
                    <select class="form-control" id="specialty_id" name="specialty_id" v-model="consultantData.specialty_id">
                        <option value="">Select Specialty</option>
                        <option v-for="specialty in specialties" :value="specialty.id" :key="specialty.id">{{ specialty.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Sex</label>
                    <select class="form-control" id="sex" name="sex" v-model="consultantData.sex">
                        <option value="">Select Sex</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="consultantData.status">
                        <option value="">Select Status</option>
                        <option value=1>Active</option>
                        <option value=0>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary" @click.prevent="editMode ? updateConsultant() : createConsultant()">{{editMode ? 'Update' : 'Create'}} Consultant</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            companies: [],
            consultantData: new Form({
                id: '',
                billing_type: '',
                company_id: '',
                email: '',
                first_name: '',
                last_name: '',
                phone: '',
                services: [],
                sex: '',
                specialty_id: '',
                status: '',
                title: '',
            }),
            loading: false,
            services: [],
            specialties: [],
        }
    },
    emits:['refreshConsultantForm'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        createConsultant(){
            this.loading = true;
            this.consultantData.post('/api/consultant_practices/consultants')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'A new consultant was created successfully',
                });
                this.$emit('refreshConsultantForm', response);
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
        getAllInitials(){
            this.loading= true;
            axios.get('/api/consultant_practices/consultants/initials')
            .then(response =>{
                this.companies = response.data.companies;
                this.services = response.data.services;
                this.specialties = response.data.specialties;
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
        updateConsultant(){
            this.loading = true;
            this.consultantData.put('/api/consultant_practices/consultants/'+this.consultantData.id)
            .then(response =>{
                this.$emit('refreshConsultantForm', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Consultant details has been modified',
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
        editMode: Boolean,
        company: {type: Object, default: null,},
        consultant: Object,
    },
    watch:{
        company(){
            if (this.company != null){this.consultantData.company_id = this.company.id}
        },
        consultant(){
            this.consultantData.fill(this.consultant);
        }
    }
}
</script>