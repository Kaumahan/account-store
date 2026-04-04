<template>
  <div class="fixed right-6 bottom-6 z-[100] font-sans antialiased">
    
    <button 
      v-show="isMinimized"
      @click="openChat"
      class="flex items-center gap-3 bg-cyan-600 hover:bg-cyan-500 text-white p-4 rounded-2xl shadow-lg transition-all transform hover:scale-110 border border-cyan-400 relative"
    >
      <span class="font-bold text-xs uppercase tracking-widest">Global Chat</span>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
      </svg>
      <span v-if="hasNewMessages" class="absolute -top-1 -right-1 flex h-4 w-4">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-[#0d121d]"></span>
      </span>
    </button>

    <aside 
      v-show="!isMinimized"
      class="h-[550px] w-85 bg-[#0d121d] border border-gray-800 rounded-2xl flex flex-col shadow-2xl overflow-hidden"
    >
      <div class="p-4 border-b border-gray-800 bg-[#151a26] flex items-center justify-between">
        <h2 class="text-sm font-bold text-cyan-400 uppercase tracking-widest">Global Chat</h2>
        <button @click="isMinimized = true" class="text-gray-500 hover:text-white"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
      </div>

      <div ref="chatBox" class="flex-1 overflow-y-auto p-4 space-y-4 bg-[#0a0e18]/50">
        <div v-for="msg in messages" :key="msg.id" class="bg-[#1c2331] p-3 rounded-xl border border-gray-700/50">
          <span class="text-[10px] font-black text-cyan-500 block uppercase">{{ msg.user?.name || 'Explorer' }}</span>
          
          <div v-if="isGif(msg.body)" class="mt-2">
            <img :src="msg.body" class="rounded-lg w-full h-auto max-h-48 object-cover border border-gray-700" alt="GIF" />
               <span class="text-[8px] font-black text-red-500 block uppercase text-right">Powered by GIPHY</span>
          </div>
          <p v-else class="text-sm text-gray-200 mt-1 leading-relaxed">{{ msg.body }}</p>
        </div>
      </div>

      <div class="p-4 bg-[#151a26] border-t border-gray-800 relative">
        
        <div v-if="showGifPicker" class="absolute bottom-full left-0 w-full bg-[#1c2331] border border-gray-700 rounded-t-xl p-3 shadow-2xl h-72 overflow-y-auto z-50">
          <input 
            v-model="gifSearchQuery" 
            @input="searchGifs" 
            placeholder="Search GIPHY..." 
            class="w-full bg-[#0a0e18] border border-gray-600 rounded-lg p-2 text-xs mb-3 text-white outline-none focus:border-cyan-500"
          >
          <div class="grid grid-cols-2 gap-2">
            <div v-for="gif in searchedGifs" :key="gif.id" class="relative group cursor-pointer" @click="sendGif(gif.images.original.url)">
                <img :src="gif.images.fixed_height_small.url" class="rounded w-full h-24 object-cover group-hover:opacity-75 transition-opacity" />
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button @click="showGifPicker = !showGifPicker" :class="showGifPicker ? 'text-cyan-400' : 'text-gray-500'" class="hover:text-cyan-400 font-black text-xs border border-current px-1.5 py-0.5 rounded transition-colors">
            GIF
          </button>
          <input 
            v-model="newMessage" 
            @keydown.enter="sendMessage()" 
            placeholder="Type a message..."
            class="flex-1 bg-[#0a0e18] border border-gray-700 rounded-xl py-2 px-4 text-sm text-white focus:border-cyan-500 outline-none"
          >
        </div>
      </div>
    </aside>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';

// States
const isMinimized = ref(true);
const hasNewMessages = ref(false);
const messages = ref([]);
const newMessage = ref('');
const chatBox = ref(null);

// GIF States
const showGifPicker = ref(false);
const gifSearchQuery = ref('');
const searchedGifs = ref([]);

// 1. Helper to detect if a message is a GIF
const isGif = (body) => {
    return typeof body === 'string' && (body.includes('giphy.com/media') || body.endsWith('.gif'));
};

// 2. Search GIFs using the API key from .env
const searchGifs = async () => {
    if (gifSearchQuery.value.length < 2) {
        searchedGifs.value = [];
        return;
    }
    
    const apiKey = import.meta.env.VITE_GIPHY_API_KEY;
    const url = `https://api.giphy.com/v1/gifs/search?api_key=${apiKey}&q=${gifSearchQuery.value}&limit=12&rating=g`;
    
    try {
        const response = await fetch(url);
        const json = await response.json();
        searchedGifs.value = json.data;
    } catch (e) {
        console.error("Giphy API Error:", e);
    }
};

// 3. Send a GIF URL as the message body
const sendGif = (url) => {
    sendMessage(url);
    showGifPicker.value = false;
    gifSearchQuery.value = '';
    searchedGifs.value = [];
};

const scrollToBottom = () => {
    nextTick(() => { if (chatBox.value) chatBox.value.scrollTop = chatBox.value.scrollHeight; });
};

const openChat = () => {
    isMinimized.value = false;
    hasNewMessages.value = false;
    scrollToBottom();
};

// window.Echo.private(`App.Models.User.${userId}`)
//     .notification((notification) => {
//         // Use a library like 'vue-toastification' or a custom Tailwind ref
//         alert(notification.message); 
//     });

const sendMessage = async (content = null) => {
    const textToSend = content || newMessage.value;
    if (!textToSend.trim()) return;

    const originalText = newMessage.value;
    if (!content) newMessage.value = ''; // Clear input if it wasn't a GIF

    try {
        const response = await axios.post('/chat', { body: textToSend });
        // Optional: messages.value.push(response.data); // Only if Reverb doesn't echo to self
        scrollToBottom();
    } catch (error) {
        console.error("Chat Error:", error);
        if (!content) newMessage.value = originalText;
    }
};

onMounted(async () => {
    // Load history
    try {
        const res = await axios.get('/chat/messages');
        messages.value = res.data;
        scrollToBottom();
    } catch (e) { console.error("History failed"); }

    // Echo listener
    if (window.Echo) {
        window.Echo.channel('public-chat').listen('MessageSent', (e) => {
            if (!messages.value.find(m => m.id === e.message.id)) {
                messages.value.push(e.message);
                if (isMinimized.value) hasNewMessages.value = true;
                scrollToBottom();
            }
        });
    }
});

</script>