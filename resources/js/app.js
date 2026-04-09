import './bootstrap';
import { createApp } from 'vue';
import ProductCard from './components/ProductCard.vue';
import ChatSidebar from './components/ChatSidebar.vue';
import ProductCredentialCard from './components/ProductCredentialCard.vue';
import ProductManager from './components/ProductManager.vue';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

const app = createApp({});

// Configuration options for your Google Login Toast
const options = {
    transition: "Vue-Toastification__bounce",
    maxToasts: 3,
    newestOnTop: true
};

app.use(Toast, options);

app.component('product-card', ProductCard);
app.component('chat-sidebar', ChatSidebar);
app.component('product-credential-card', ProductCredentialCard);

// Register the Modal
// Register the Manager (Table) if you plan to use it on an admin page
app.component('product-manager', ProductManager);

app.mount('#app');