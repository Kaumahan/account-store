<template>
  <aside v-show="isOpen" class="h-[750px] w-96 bg-[#0d121d] border border-gray-800 rounded-3xl flex flex-col shadow-2xl overflow-hidden">
    <div class="p-5 border-b border-gray-800 bg-[#151a26] flex items-center justify-between">
      <div>
         <h2 class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em]">Inventory Core</h2>
         <p class="text-[9px] text-gray-500 uppercase font-bold">Protocol: Listing v2.4</p>
      </div>
      <button @click="isOpen = false" class="text-gray-500 hover:text-white p-2">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </button>
    </div>

    <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-[#0a0e18]/80">
      <div class="bg-[#1c2331]/50 p-4 rounded-2xl border border-gray-800">
        <label class="text-[10px] font-black text-gray-500 uppercase mb-2 block">Seller Base Price (PHP)</label>
        <div class="relative">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">₱</span>
          <input v-model="form.price" type="number" placeholder="0.00" class="w-full bg-[#0d121d] border border-gray-700 rounded-xl py-3 pl-10 pr-4 text-lg font-bold text-emerald-400 focus:border-emerald-500 outline-none transition-all">
        </div>
      </div>

      <div v-if="form.price > 0" class="space-y-3">
        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest px-1">Network Transmission Fees</label>
        
        <div class="bg-[#0d121d] border border-gray-800 rounded-2xl p-4 space-y-3 relative overflow-hidden">
          <div class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-500/10 blur-2xl"></div>

          <div class="flex justify-between items-center text-xs">
            <div class="flex items-center gap-2">
              <div class="w-1.5 h-1.5 rounded-full bg-emerald-500/50"></div>
              <span class="text-gray-400">Gateway Tax (3.5% + 15)</span>
            </div>
            <span class="font-mono text-emerald-400">+₱{{ fees.paymongo.toFixed(2) }}</span>
          </div>

          <div class="flex justify-between items-center text-xs">
            <div class="flex items-center gap-2">
              <div class="w-1.5 h-1.5 rounded-full bg-cyan-500/50"></div>
              <span class="text-gray-400">Software Gas Fee</span>
            </div>
            <span class="font-mono text-cyan-400">+₱5.00</span>
          </div>

          <div class="pt-3 mt-1 border-t border-gray-800 flex justify-between items-end">
            <div>
              <p class="text-[9px] font-black text-gray-500 uppercase tracking-tighter">Market Display Price</p>
              <p class="text-xs text-gray-400 font-medium italic">Visible to buyers</p>
            </div>
            <div class="text-right">
              <span class="text-2xl font-black text-white leading-none">
                ₱{{ fees.totalPrice.toLocaleString(undefined, {minimumFractionDigits: 2}) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-4 pt-2">
        <div class="group">
          <label class="text-[10px] font-black text-gray-500 uppercase mb-2 block">Product Identity</label>
          <input v-model="form.name" type="text" placeholder="e.g. Immortal Valorant Account" class="w-full bg-[#1c2331] border border-gray-800 rounded-xl py-3 px-4 text-sm focus:border-emerald-500 outline-none">
        </div>

        <div>
          <label class="text-[10px] font-black text-gray-500 uppercase mb-2 block">Product Asset URL</label>
          <input v-model="form.image_url" type="text" placeholder="https://..." class="w-full bg-[#1c2331] border border-gray-800 rounded-xl py-3 px-4 text-sm focus:border-emerald-500 outline-none">
        </div>
      </div>
    </div>

    <div class="p-5 bg-[#151a26] border-t border-gray-800">
      <button 
        @click="submitProduct"
        :disabled="isSubmitting || !form.price"
        class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:opacity-30 text-white font-black py-4 rounded-2xl text-xs uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-2"
      >
        <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        {{ isSubmitting ? 'Authorizing...' : 'Deploy to Market' }}
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref, reactive, computed } from "vue";
import axios from "axios";

const isOpen = ref(false);
const isSubmitting = ref(false);

const form = reactive({
  name: "",
  description: "",
  price: 0,
  stock: 1, // Default to 1 for account listings
  image_url: "",
  is_active: true,
});

const fees = computed(() => {
  const base = parseFloat(form.price) || 0;
  const gasFee = 5.00;
  
  // PayMongo Tax: 3.5% + 15 PHP
  const pmTax = (base * 0.035) + 15;
  
  return {
    paymongo: pmTax,
    gas: gasFee,
    totalPrice: base + pmTax + gasFee
  };
});

const submitProduct = async () => {
  if (!form.name.trim() || !form.price) return;
  isSubmitting.value = true;

  try {
    const response = await axios.post("/inventory/store", {
      ...form,
      display_price: fees.value.totalPrice, // The final price buyers see
      base_price: form.price,               // What the seller earns
      is_active: form.is_active ? 1 : 0,
    });

    if (response.status === 200 || response.status === 201) {
      isOpen.value = false;
      Object.assign(form, { name: "", description: "", price: 0, stock: 1, image_url: "", is_active: true });
    }
  } catch (e) {
    console.error(e);
  } finally {
    isSubmitting.value = false;
  }
};
</script>