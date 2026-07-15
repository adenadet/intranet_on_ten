<template>
    <div>
        <form> 
        <alert-error :form="updateData"></alert-error> 
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status_id" name="status_id" v-model="updateData.status_id" :class="{'is-invalid' : updateData.errors.has('status_id') }">
                        <option value="">---Select Status---</option>
                        <option v-for="status in statuses" :value="status.id" :key="status.id">{{status.name}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12 col-sm-12">
                <div class="form-group">
                    <label>Update</label>
                    <textarea rows="6" class="form-control" v-model="updateData.content"></textarea>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <input type="submit" name="submit" class="submit btn btn-success" value="Update" @click.prevent="updateTicket"/>
            </div>
        </div>
        </form>
    </div>
</template>
<script>
export default {    
    data(){
        return {  
            updateData: new Form({
                agent_id:'', 
                close: false,
                content: '',
                department_id: '', 
                status_id: '',
                ticket_id:'',
                ticket_status: '',
                user_id: '',
            }),
            departments: [],
            loading: false,
            route: '',
            users: [],
        }
    },
    emits:['refreshReplyForm'],
    methods:{
        closeTicket(){
            this.loading = true;
            this.submitUpdate();
            this.$swal.fire({icon: 'success',title: 'Ticket has been updated',});
        },
        updateTicket(){
            this.loading = true;
            this.updateData.ticket_id = this.ticket.id;
            this.updateData.post('/api/tickets/comments')
            .then(response=>{
                this.$emit('refreshReplyForm');
                this.updateData.reset();
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error',title: 'Your form was not sent try again later!',});
            })
            .finally(() => {
                this.loading = false;
            })
        },    
        assignUsers(){
            this.loading = true;
            this.AssignData.post('/api/lms/assign_users')
            .then(response=>{
                this.$emit('refreshReplyForm');
                this.AssignData.reset();        
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error',title: 'Your form was not sent try again later!',});
            })
            .finally(()=>{
                this.loading = false;
            })
        },
        getInitials(){
            axios.get('/api/lms/assign_users').then(response =>{
                this.departments = response.data.departments;
                this.users = response.data.users;
            })
            .catch(()=>{
                toast.fire({
                    icon: 'error',
                    title: 'Departments were not loaded successfully',
                })
            });
        },
        updateList(){
            if (in_array(-1, this.AssignData.user_id)){
                console.log('Yue');

            }
            else{

            }
            console.log(this.AssignData.user_id);
            
        },
        updateUsers(){
            this.AssignData.dept_id = this.departments[this.AssignData.department_id].id;
            if (this.AssignData.department_id != 1000){
                this.AssignData.user_id = [];
                this.users = this.departments[this.AssignData.department_id].users;
            }
            console.log(this.AssignData.department_id); 
        },
         
    },
    mounted() {
        this.getInitials();
    },
    props: {
        'editMode': Boolean,
        'statuses': Array,
        'ticket': Object,
    },
}
</script>