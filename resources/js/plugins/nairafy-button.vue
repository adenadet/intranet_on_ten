<template>
    <button @click="openIframe"><slot>Test Pay with Nairafy</slot></button>

    <div v-if="showIframe" class="nairafy-iframe-modal">
        <div class="nairafy-iframe-container">
            <iframe :src="iframeUrl" frameborder="0" width="100%" height="600px"></iframe>
            <button @click="showIframe = false">Close</button>
        </div>
    </div>
</template>

<!--script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  firstName: String,
  lastName: String,
  email: String,
  phone: String,
  vendorId: String,
  uniqueId: String
})

const showIframe = ref(false)

const iframeUrl = computed(() => {
  return `https://dashboard.nairafy.ng/pay/${props.uniqueId}`
})

function openIframe() {
  showIframe.value = true
}


</script -->
<script>
export default {
    computed: {
        iframeUrl() {
            return `https://dashboard.nairafy.ng/pay/${this.uniqueId}/${this.vendorId}`;
        }
    },
    data(){
        return {
            showIframe: false,
            current_page: 1,
            departments: [],
            editMode: false,
            form: new Form({}),
            loading: false,
            notice: {},
            notices: {},
        }
    },
    methods:{
        openIframe() {
            this.showIframe = true;
        },
        /*createNotice(){
            this.editMode = false;
            this.notice = {};
            //Fire.$emit('noticeDataFill', this.notice);
            $('#noticeModal').modal('show');
        },
        deleteNotice(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/notices/'+id)
                    .then(response=>{
                    Swal.fire('Deleted!', 'Notice has been deleted.', 'success');
                    Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});});
                }
            }); 
        },
        editNotice(notice){
            this.editMode = true;
            this.notice = notice;
            //Fire.$emit('noticeDataFill', notice);
            $('#noticeModal').modal('show');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/notices?t=all&page='+page).then(response =>{
                this.reset(response);
                this.loading = false;
                toast.fire({icon: 'success', title: 'Notice loaded successfully',});
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({icon: 'error', title: 'Notice not loaded successfully',});
            });
        },
        reset(response){
            this.categories = response.data.categories;
            this.departments = response.data.departments;
            this.notices = response.data.notices;
        },*/
    },
    mounted() {
        this.getAllInitials();
        //Fire.$on('reloadNotice', response =>{this.reset(response); console.log("Updated")});
    },
    props: {
        firstName: String,
        lastName: String,
        email: String,
        phone: String,
        vendorId: String,
        uniqueId: String,
    }
}
</script>

<style scoped>
.nairafy-iframe-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.nairafy-iframe-container {
    background: white;
    width: 90%;
    max-width: 700px;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
}
.nairafy-iframe-container button {
    position: absolute;
    top: 10px;
    right: 10px;
}
</style>
