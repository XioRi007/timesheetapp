<script setup>
defineEmits(['update:modelValue'])
defineProps({
  modelValue: {
    type: Boolean,
    required: true
  },
  activeText: {
    type: String,
    required: true
  },
  notActiveText: {
    type: String,
    required: true
  }
})
</script>
<template>
  <div class="flex items-center" @click="$emit('update:modelValue', !modelValue)">
    <div class="w-20">
      <span v-if="modelValue">{{ activeText }}</span>
      <span v-else>{{ notActiveText }}</span>
    </div>
    <div :class="{ 'bg-green-400': modelValue}"
         class="w-20 h-10 flex items-center bg-gray-300 rounded-full p-1 duration-300 ease-in-out">
      <div :class="{ 'translate-x-10': modelValue,}"
           class="bg-white w-8 h-8 rounded-full shadow-md transform duration-300 ease-in-out"></div>
    </div>
  </div>
</template>
