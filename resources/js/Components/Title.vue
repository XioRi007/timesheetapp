<script setup>
defineProps({
  text: {
    type: String,
  },
})
</script>
<template>
  <h1 class="font-semibold text-xl text-gray-800 leading-tight px-8">{{ text }}</h1>
</template>
