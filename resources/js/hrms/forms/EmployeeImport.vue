<template>
    <section class="overlay-wrapper p-0">
        <form role="form" @submit.prevent="uploadEmployees">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Type</label>
                        <select type="email" class="form-control" name="upload_type" id="upload_type">
                            <option value="">--Select Upload Type</option>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>File</label>
                        <input type="file" class="form-control" name="upload_file" id="upload_file" @change="uploadFile">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="/samples/hrms/sample_upload_employees.xlsx"  class="btn btn-success float-right" target="_blank" download><i class="fa fa-download mr-1"></i>Download Sample Excel</a>
                </div>
            </div>
        </form>
    </section>
</template>
<script>
export default {
    data() {
        return {
            employeeData: new Form({
                type: '',
                file: '',
            }),
            loading: false,
        }
    },
    emits:['refreshPage'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        uploadEmployees(){
            this.loading = true;
            this.employeeData.post('/api/hrms/employees/import')
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Employees have been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        uploadFile(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.employeeData.file = reader.result;
                    }
                reader.readAsDataURL(file)
            }
            else{this.$swal.fire({type: 'error', title: 'File is too large'});}
        },
    },
}
</script>