<template>
    <div class="card m-0">
        <div class="card-header bg-success"><h3 class="card-title">Latest Staffs</h3></div>
        <div class="card-body overlay-wrapper p-0">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <ul class="users-list clearfix">
                <li v-for="user in staffs" :key="user.id">
                    <img style="height: 50px;" :src="(user.image) ? '/img/profile/'+user.image : '/img/profile/default.png'" :title="user.first_name+' '+user.last_name" :alt="user.first_name+' '+user.last_name">
                    <a :href="'/contacts/'+user.id" class="users-list-name">{{user.first_name+' '+user.last_name}}</a>
                    <span class="users-list-date">{{ExcelDateMonth(user.joined_at) }}</span>
                </li>
            </ul>
        </div>
        <div class="card-footer text-center">
            <router-link to="/contacts">See More</router-link>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            loading: false,
            staffs: [],
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/ums/staffs/latest')
            .then(response =>{
                this.staffs = response.data.staffs;
                this.loading = false;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Staff not loaded successfully',});
                this.loading = false;
            });
        },
        scrollHanle(evt) {
            console.log(evt)
        },
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>
<style >
.scroll-area {
    position: relative;
    margin: auto;
    width: 600px;
    height: 400px;
}
</style>