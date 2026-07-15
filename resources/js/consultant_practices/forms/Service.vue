<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>Name</label>
                    <input required type="text" class="form-control" id="name" v-model="serviceData.name" placeholder="Enter service name">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>ICP Code</label>
                    <input required type="text" class="form-control" id="icp_code" v-model="serviceData.icp_code" placeholder="Enter ICP code">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Specialty</label>
                    <select class="form-control" id="specialty_id" name="specialty_id" v-model="serviceData.specialty_id" required>
                        <option value="">Select Specialty</option>
                        <option v-for="specialty in specialties" :key="specialty.id" :value="specialty.id">{{ specialty.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="serviceData.status" required>
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor class="form-control" id="description" v-model:content="serviceData.description" placeholder="Enter service description" theme="snow" content-type="html"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary" @click.prevent="editMode ? updateService() :createService()">{{editMode ? 'Update' : 'Create'}} Service</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';
export default {
    components: {ModelListSelect},
    data(){
        return  {
            consultants: [],
            loading: false,
            serviceData: new Form({
                id: '',
                icp_code: '',
                name: "",
                specialty_id: "",
                description: "",
                status: "",
            }),
            specialties: [],
        }
    },
    emits:['refreshServiceForm'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        codeAndNameAndDesc(item){
            return `${item.last_name}, ${item.first_name} ${item.middle_name}`
        },
        createService(){
            this.loading = true;
            this.serviceData.post('/api/consultant_practices/services')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'A new service was created successfully',
                });
                this.$emit('refreshServiceForm', response);
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
            axios.get('/api/consultant_practices/services/initials')
            .then(response =>{
                this.specialties = response.data.specialties;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Service form not loaded successfully',
                })
            });
        },
        updateService(){
            this.loading = true;
            this.serviceData.put('/api/consultant_practices/services/'+this.serviceData.id)
            .then(response =>{
                this.$emit('refreshServiceForm', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Service details has been modified',
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
        service: Object,
    },
    watch:{
        service(){
            this.serviceData.fill(this.service);
        }
    }
}
</script>