<script setup>
import { ref, onMounted, nextTick } from "vue";
import axios from "axios";

const messages = ref([]);
const newMessage = ref("");
const chatBox = ref(null);

const scrollToBottom = () => {
  nextTick(() => {
    if (chatBox.value) chatBox.value.scrollTop = chatBox.value.scrollHeight;
  });
};

onMounted(async () => {
  // 1. Fetch History
  try {
    const res = await axios.get("/chat/messages");
    messages.value = res.data;
    scrollToBottom();
  } catch (e) {
    console.error("History fetch failed");
  }

  // 2. Real-time Listener
  if (window.Echo) {
    window.Echo.channel("public-chat")
      .listen(".MessageSent", (e) => {
        console.log("Payload received:", e); // Check this in F12 Console
        
        // In Laravel, the payload is usually e.message
        if (e.message) {
          // Check for duplicates to prevent double-displaying for the sender
          const isDuplicate = messages.value.some(m => m.id === e.message.id);
          if (!isDuplicate) {
            messages.value.push(e.message);
            scrollToBottom();
          }
        }
      });
  }
});

const sendMessage = async () => {
  if (!newMessage.value.trim()) return;

  const payload = { body: newMessage.value }; // Changed to 'body'
  newMessage.value = ""; 

  try {
    const response = await axios.post("/chat", payload);
    
    // Manual push for the sender (if using ->toOthers() in Laravel)
    if (!messages.value.find(m => m.id === response.data.id)) {
      messages.value.push(response.data);
      scrollToBottom();
    }
  } catch (error) {
    console.error("Message failed to send", error);
  }
};
</script>

<template>
  <div class="fixed bottom-4 right-4 w-80 bg-gray-900 border border-gray-700 rounded-lg shadow-2xl overflow-hidden flex flex-col">
    <div class="p-3 bg-emerald-600 font-bold text-white text-xs uppercase">Global Chat</div>
    
    <div ref="chatBox" class="h-64 overflow-y-auto p-4 space-y-3 bg-[#0a0e14]">
      <div v-for="msg in messages" :key="msg.id" class="flex flex-col">
        <span class="text-cyan-500 font-bold text-[10px] uppercase">
          {{ msg.user?.name || 'Explorer' }}
        </span>
        <div class="bg-gray-800 p-2 rounded text-sm text-gray-200 mt-1 border border-gray-700/30">
          {{ msg.body }} 
        </div>
      </div>
    </div>

    <div class="p-2 border-t border-gray-700 bg-gray-900 flex">
      <input
        v-model="newMessage"
        @keyup.enter="sendMessage"
        class="flex-1 bg-gray-800 text-white text-xs border-none focus:ring-1 focus:ring-emerald-500 rounded p-2 outline-none"
        placeholder="Say something..."
      />
    </div>
  </div>
</template>