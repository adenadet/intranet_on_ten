<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="companyData.name" placeholder="Enter company name">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="companyData.status">
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="email" name="email" v-model="companyData.email">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" v-model="companyData.phone">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Address</label>
                    <QuillEditor class="form-control" id="address" name="address" v-model:content="companyData.address" content-type="html" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary" @click.prevent="createCompany" v-if="!editMode">Create Company</button>
                <button type="button" class="btn btn-primary" @click.prevent="updateCompany" v-if="editMode">Update Company</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
import { QuillEditor } from '@vueup/vue-quill';

export default {
    data(){
        return  {
            companyData: new Form({
                id: '',
                address: '',
                email: '',
                name: '',
                phone: '',
                status: '',
            }),
            loading: false,
        }
    },
    emits:['refreshCompanyForm'],
    methods:{
        createCompany(){
            this.loading = true;
            this.companyData.post('/api/consultant_practices/companies')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'A new company was created successfully',
                });
                this.$emit('refreshCompanyForm', response);
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
        updateCompany(){
            this.loading = true;
            this.companyData.put('/api/consultant_practices/companies/'+this.companyData.id)
            .then(response =>{
                this.$emit('refreshCompanyForm', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Company details has been modified',
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
        company: Object,
    },
    watch:{
        company(){
            this.companyData.fill(this.company);
        }
    }
}
</script>