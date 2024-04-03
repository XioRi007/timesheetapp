<script setup>
import {onMounted, ref} from 'vue'

defineProps({
  modelValue: {
    type: [String, Number, null],
    required: true,
  }
})

defineEmits(['update:modelValue'])

const input = ref(null)

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus()
  }
})

defineExpose({focus: () => input.value.focus()})
</script>

<template>
  <input
    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
    :value="modelValue"
    ref="input"
    @change="$emit('update:modelValue', $event.target.value === '' ? null : $event.target.value)"
  />
</template>