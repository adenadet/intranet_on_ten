<template>
<section class="card overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card-body">
        <form role="form">
            <alert-error :form="publicHolidayData"></alert-error> 
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Date</label>
                        <input class="form-control" type="date" name="date" id="date" v-model="publicHolidayData.date" />
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="status" v-model="publicHolidayData.status">
                            <option value="">--Select Status--</option>
                            <option value=1>Active</option>
                            <option value=0>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <button @click.prevent="editMode ? updatePublicHoliday() : createPublicHoliday()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
        </form>
    </div> 
</section>
</template>
<script>
export default {
    data() {
        return {
            publicHolidayData: new Form({
                date: '', 
                id: '',
                status:'',
            }),
            loading: false,
        }
    },
    emits:['refreshPage'],
    mounted() {},
    methods: {
        createPublicHoliday(){
            this.loading = true;
            this.publicHolidayData.post('/api/hrms/public_holidays')
            .then(() =>{
                this.loading = false;
                this.$emit('refreshPage');
                this.$swal.fire({icon: 'success', title: 'The Public Holiday has been created', showConfirmButton: false, timer: 1500});
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
        updatePublicHoliday(){
            this.loading = true;
            this.publicHolidayData.put('/api/hrms/public_holidays/'+this.public_holiday.id)
            .then(() =>{
                this.$emit('refreshPage');
                this.$swal.fire({icon: 'success', title: 'The Public Holiday has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;    
        },
    },
    props: {
        editMode: Boolean,
        public_holiday: Object,
    },
    watch:{
        public_holiday(){
            this.publicHolidayData.fill(this.public_holiday);
        }
    }
}
</script>