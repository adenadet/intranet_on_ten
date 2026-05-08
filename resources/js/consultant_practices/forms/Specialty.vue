<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="specialtyData.name" placeholder="Enter specialty name">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="specialtyData.status">
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
                    <QuillEditor class="form-control" id="description" name="description" v-model:content="specialtyData.description" content-type="html" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary" @click.prevent="editMode ? updateSpecialty() :createSpecialty()">{{editMode ? 'Update' : 'Create'}} Create Specialty</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            specialtyData: new Form({
                id: '',
                description: '',
                name: '',
                status: '',
            }),
            loading: false,
        }
    },
    emits:['refreshSpecialtyForm'],
    methods:{
        createSpecialty(){
            this.loading = true;
            this.specialtyData.post('/api/consultant_practices/specialties')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'A new specialty was created successfully',
                });
                this.$emit('refreshSpecialtyForm', response);
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
        updateSpecialty(){
            this.loading = true;
            this.specialtyData.put('/api/consultant_practices/specialties/'+this.specialtyData.id)
            .then(response =>{
                this.$emit('refreshSpecialtyForm', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Specialty details has been modified',
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
        specialty: Object,
    },
    watch:{
        specialty(){
            this.specialtyData.fill(this.specialty);
        }
    }
}
</script>