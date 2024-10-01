import './bootstrap';
import {createRouter, createWebHistory} from 'vue-router';

import router, { registerGlobalComponents } from './router';
import { createApp, ref } from 'vue';
import _ from 'lodash';
import { globalMethods } from './globalMethods'; 

const app = createApp({
    setup() {
        const search = ref('');
        const searchIt = _.debounce(() => {Fire.$emit('searchInstance');}, 1000);
        return {search, searchIt};
    }
});

registerGlobalComponents(app);

import VueDocumentEditor from 'vue-document-editor'
app.component(VueDocumentEditor);

import { Carousel, Slide as CarouselSlide, Navigation as CarouselNavigation,  Pagination as CarouselPagination} from 'vue3-carousel';
import {} from 'vue3-carousel';
app.component('Carousel', Carousel);
app.component('CarouselNavigation', CarouselNavigation);
app.component('CarouselSlide', CarouselSlide);
app.component('CarouselPagination', CarouselPagination);

import Form from "vform";
window.Form = Form;
import {Button, HasError, AlertError, AlertErrors, AlertSuccess} from 'vform/src/components/bootstrap5'
app.component(Button.name, Button)
app.component(HasError.name, HasError)
app.component(AlertError.name, AlertError)
app.component(AlertErrors.name, AlertErrors)
app.component(AlertSuccess.name, AlertSuccess)

import Pagination from 'v-pagination-3';
app.component('pagination', Pagination);

//Quill Editor
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
app.component('QuillEditor', QuillEditor);

//Search Select
import { ModelListSelect } from 'vue-search-select';
import "vue-search-select/dist/VueSearchSelect.css";
app.component('ModelListSelect', ModelListSelect);
//Signature Pad
import VueSignaturePad from 'vue-signature-pad';
app.use(VueSignaturePad);


//Sweetalert 
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';

// Define the toast
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

app.config.globalProperties.$form = Form;
app.config.globalProperties.$swal = Swal;
app.config.globalProperties.$toast = toast;

app.mixin(globalMethods);
app.use(router);
app.mount('#corner');