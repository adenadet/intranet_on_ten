<template>
<div class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>  
    <form id="register_form">  
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>User Name *</label>
                    <input type="text" class="form-control" id="nok_name" name="nok_name" placeholder="Full Name *" disabled :value="user.first_name+' '+ (user.middle_name != null ? user.middle_name : '')   +' '+user.last_name" />
                    <input type="hidden" v-model="userRoleForm.user_id" name="user_id" id="user_id" />
                </div>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-12">Roles</label>
            <div class="col-sm-3" v-for="role in roles" :key="role.id">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="roles[]" id="roles[]" v-model="userRoleForm.roles" :value="role.id" :checked="userRoleForm.roles.includes(role.id)">
                    <label class="form-check-label">{{role.name}}</label>
                </div>
            </div> 
        </div>    
        <button @click.prevent="updateUserRole" type="submit" name="submit" class="submit btn btn-success">Submit </button>
    </form>

</div>
</template>
<script>
import Form from 'vform';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';

export default {
    data(){
        return {
            editMode:false,
            loading: false,
            roles: {},
            userRoleForm: new Form({
                user_id: '',
                roles: [],
            }),
        }
    },
    emits: ['refreshPage'],
    methods:{
        getAllInitials(){           
            axios.get('/api/ums/roles/initials').then(response =>{
                this.roles = response.data.roles;
            })
            .catch(()=>{
                toast.fire({icon: 'error', title: 'Roles not loaded successfully',});
            });
        },
        updateUserRole(){
            this.loading = true;
            this.userRoleForm.post('/api/ums/users/roles')
            .then(response =>{
                Swal.fire({icon: 'success', title: 'The Role(s) have been assigned', showConfirmButton: false, timer: 1500});
                this.$emit('refreshPage', response);
                this.loading =false;
                })
            .catch(()=>{
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.loading =false;
            });         
        },
    },
    mounted() {
        this.getAllInitials();
    },
    props:{
        user: Object,
    },
    watch:{
        user(){
            this.userRoleForm.user_id = this.user.id;
            this.userRoleForm.roles = [];
            for (let i = 0; i < this.user.roles.length; i++) {
                this.userRoleForm.roles.push(this.user.roles[i].id);
            } 
        }
    }
}
</script>