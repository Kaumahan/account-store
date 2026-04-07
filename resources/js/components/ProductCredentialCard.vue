<template>
  <div class="bg-[#151a26] border border-gray-800 rounded-2xl overflow-hidden shadow-2xl transition-all hover:border-cyan-900/30">
    <div class="p-5 bg-[#1c2331] border-b border-gray-800 flex justify-between items-center">
      <div>
        <h3 class="text-lg font-bold text-white tracking-tight">{{ product.name }}</h3>
        <p class="text-[9px] font-black text-cyan-500 uppercase tracking-[0.2em]">Asset Unlocked</p>
      </div>
      <div class="bg-black/20 px-3 py-1 rounded-full border border-gray-700">
        <span class="text-[9px] text-gray-500 font-mono">ID: #{{ product.id }}</span>
      </div>
    </div>

    <div class="p-6 space-y-6">
      <div class="space-y-4">
        <div class="flex flex-col gap-1">
          <span class="text-[10px] uppercase text-gray-500 font-black tracking-widest">Account Details</span>
          <div class="bg-black/40 border border-gray-800 rounded-xl p-4">
            <p class="text-sm font-mono text-emerald-400 break-all leading-relaxed">
              {{ credentials.account_data || 'No data received' }}
            </p>
          </div>
        </div>

        <div v-if="credentials.guide" class="flex flex-col gap-1">
          <span class="text-[10px] uppercase text-gray-500 font-black tracking-widest">Setup Guide</span>
          <p class="text-xs text-gray-400 italic leading-relaxed bg-cyan-950/10 p-3 rounded-lg border border-cyan-900/20">
            {{ credentials.guide }}
          </p>
        </div>
      </div>

      <button 
        @click="copyToClipboard" 
        class="w-full py-3 bg-gray-800 hover:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center justify-center gap-2"
      >
        <span>{{ copied ? '✅ Copied' : '📋 Copy Plain Text' }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  product: Object,
  credentials: Object
});

const copied = ref(false);

const copyToClipboard = () => {
  if (!props.credentials.account_data) return;
  
  navigator.clipboard.writeText(props.credentials.account_data);
  copied.value = true;
  setTimeout(() => copied.value = false, 2000);
};

// Debugging: This will show up in your F12 Console in Chrome
onMounted(() => {
  console.log(`Vault Item [${props.product.name}]:`, props.credentials.account_data);
});
</script>