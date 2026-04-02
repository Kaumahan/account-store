import './bootstrap';
import { createApp } from 'vue'; 
import ProductCard from './components/ProductCard.vue';
import ChatSidebar from './components/ChatSidebar.vue';

const app = createApp({});

// This name MUST be 'product-card' to match your Blade code
app.component('product-card', ProductCard);
app.component('chat-sidebar', ChatSidebar);

app.mount('#app');