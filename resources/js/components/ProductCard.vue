<template>
  <div
    class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10"
  >
    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
      <img
        :src="product.image_url || '/placeholder-game.jpg'"
        :alt="product.name"
        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
      />
      
      <div class="absolute top-3 left-3 flex gap-2">
        <div v-if="product.stock > 0" class="flex items-center gap-1.5 rounded-full bg-emerald-500 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-lg">
          <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
          {{ product.stock }} Available
        </div>
        <div v-else class="rounded-full bg-slate-900/80 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur-md">
          Sold Out
        </div>
      </div>
    </div>

    <div class="flex flex-1 flex-col p-5">
      <div class="mb-1.5 flex items-center justify-between">
        <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-500">Account Listing</span>
        
        <div class="flex items-center text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
          <svg class="h-3 w-3 fill-current" viewBox="0 0 20 20">
            <path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
          </svg>
          <span class="ml-1 text-[9px] font-black uppercase tracking-tighter">Verified</span>
        </div>
      </div>

      <h3 class="mb-1 text-base font-bold text-slate-900 line-clamp-1 group-hover:text-indigo-600 transition-colors">
        {{ product.name }}
      </h3>

      <div class="mb-3 flex items-center gap-1.5">
        <div class="flex items-center">
          <template v-for="star in 5" :key="star">
            <svg 
              class="h-3.5 w-3.5" 
              viewBox="0 0 20 20"
              :class="star <= Math.floor(dummyRating) ? 'text-amber-400 fill-current' : 'text-slate-200 fill-current'"
            >
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
          </template>
        </div>
        <div class="flex items-center gap-1">
          <span class="text-[11px] font-black text-slate-700">{{ dummyRating }}</span>
          <span class="text-[10px] font-bold text-slate-400">({{ dummyReviewCount }} Reviews)</span>
        </div>
      </div>

      <p class="mb-4 text-xs leading-relaxed text-slate-500 line-clamp-2">
        {{ product.description }}
      </p>

      <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
        <div>
          <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Price</p>
          <p class="text-xl font-black text-slate-900">₱{{ numberFormat(product.price) }}</p>
        </div>

        <button
          @click="handlePayment(product.id)"
          :disabled="product.stock <= 0"
          :class="[
            'flex items-center justify-center rounded-xl px-6 py-2.5 text-xs font-black uppercase tracking-widest transition-all',
            product.stock > 0
              ? 'bg-indigo-600 text-white hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 active:scale-95'
              : 'bg-slate-100 text-slate-400 cursor-not-allowed',
          ]"
        >
          {{ product.stock > 0 ? "Buy Now" : "Out" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useToast } from "vue-toastification";

const numberFormat = (val) => new Intl.NumberFormat().format(val);

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  isLoggedIn: {
    type: Boolean,
    default: false,
  },
});

// Dummy Data for Launch
const dummyRating = 5;
const dummyReviewCount = 12;

const toast = useToast();

const handlePayment = (id) => {
  if (!props.isLoggedIn) {
    toast.error("Please login with Google to continue", {
      timeout: 3000,
      position: "top-center",
      closeOnClick: true,
      pauseOnHover: true,
    });
    return;
  }

  // Create CSRF hidden form for secure RoadRunner/Laravel processing
  const form = document.createElement("form");
  form.method = "POST";
  form.action = `/checkout/${id}`;

  const token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

  if (token) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "_token";
    input.value = token;
    form.appendChild(input);
  }

  document.body.appendChild(form);
  form.submit();
};
</script>