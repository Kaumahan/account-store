<template>
  <div class="group relative flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white transition-all hover:shadow-lg hover:-translate-y-1">
    <div class="aspect-square overflow-hidden bg-slate-100">
      <img 
        :src="product.image_url" 
        :alt="product.name" 
        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
      />
      <div class="absolute top-3 left-3">
        <span v-if="product.stock > 0" class="rounded-full bg-emerald-500/90 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur-sm">
          In Stock {{ product.stock }}
        </span>
        <span v-else class="rounded-full bg-rose-500/90 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur-sm">
          Sold Out
        </span>
      </div>
    </div>

    <div class="flex flex-1 flex-col p-4">
      <div class="mb-2 flex items-start justify-between">
        <h3 class="text-sm font-medium text-slate-900 line-clamp-1">
          {{ product.name }}
        </h3>
        <p class="text-lg font-bold text-indigo-600">₱{{ product.price }}</p>
      </div>

      <p class="mb-4 text-xs text-slate-500 line-clamp-2 italic">
        {{ product.description }}
      </p>

      <button 
        @click="handlePayment(product.id)"
        :disabled="product.stock <= 0"
        class="mt-auto w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors"
        >
        GCash / Card
        </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  product: {
    type: Object,
    required: true
  }
});

const handlePayment = (id) => {
    // 1. Create a dynamic form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/checkout/${id}`;

    // 2. Get the CSRF token from the meta tag
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (token) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_token';
        input.value = token;
        form.appendChild(input);
    }

    // 3. Append and submit
    document.body.appendChild(form);
    form.submit();
};
</script>