<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" v-model="patientData.name" placeholder="Enter patient name">
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label>Unique ID/ EMR No.</label>
                    <input type="text" class="form-control" id="unique_id" v-model="patientData.unique_id" placeholder="Enter unique ID">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Type</label>
                    <select class="form-control" id="patient_type" name="patient_type" v-model="patientData.patient_type">
                        <option value="">Select Patient Type</option>
                        <option value="cash">Cash</option>
                        <option value="hmo">Insurance</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Sex</label>
                    <select class="form-control" id="sex" name="sex" v-model="patientData.sex">
                        <option value="">Select Sex</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="patientData.status">
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary" @click.prevent="createPatient" v-if="!editMode">Create Patient</button>
                <button type="button" class="btn btn-primary" @click.prevent="updatePatient" v-if="editMode">Update Patient</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            consultants: [],
            loading: false,
            patientData: new Form({
                id: '',
                name: "",
                unique_id: "",
                patient_type: "",
                sex: "",
                status: "",
            }),
            specialties: [],
        }
    },
    emits:['refreshPatientForm'],
    mounted() {
    },
    methods:{
        createPatient(){
            this.loading = true;
            this.patientData.post('/api/consultant_practices/patients')
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'A new patient was created successfully',
                });
                this.$emit('refreshPatientForm', response);
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
        updatePatient(){
            this.loading = true;
            this.patientData.put('/api/consultant_practices/patients/'+this.patientData.id)
            .then(response =>{
                this.$emit('refreshPatientForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Patient details has been modified',
                    showConfirmButton: false,
                    timer: 1500
                });
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
    },
    props:{
        editMode: Boolean,
        patient: Object,
    },
    watch:{
        patient(){
            this.patientData.fill(this.patient);
        }
    }
}
</script>