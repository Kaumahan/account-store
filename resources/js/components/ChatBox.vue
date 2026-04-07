<script setup>
import { ref, onMounted } from "vue";

const messages = ref([]);
const newMessage = ref("");

window.Echo.channel("public-chat").listen(".MessageSent", (e) => {
  // The dot is the secret sauce
  console.log("Real-time message arrived!", e);
  messages.value.push(e.message);
});

const sendMessage = () => {
  axios.post("/chat/send", { message: newMessage.value });
  newMessage.value = "";
};
</script>

<template>
  <div
    class="fixed bottom-4 right-4 w-80 bg-gray-900 border border-gray-700 rounded-lg shadow-2xl overflow-hidden"
  >
    <div class="p-3 bg-emerald-600 font-bold text-white">Support Chat</div>
    <div class="h-64 overflow-y-auto p-4 space-y-2">
      <div
        v-for="msg in messages"
        :key="msg.id"
        class="bg-gray-800 p-2 rounded text-sm text-gray-200"
      >
        {{ msg.text }}
      </div>
    </div>
    <div class="p-2 border-t border-gray-700 flex">
      <input
        v-model="newMessage"
        @keyup.enter="sendMessage"
        class="flex-1 bg-gray-800 text-white text-xs border-none focus:ring-0"
        placeholder="Type..."
      />
    </div>
  </div>
</template>
