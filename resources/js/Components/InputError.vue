<script setup>
defineProps({
  message: {
    type: String,
  },
})
const convertFirstWordToTitleCase = (str) => {
  if (!str) return ''
  const _words = str.split(' ')
  const firstWords = _words[0]
  const words = firstWords.split('_')
  if (words[0] === 'hrs') return 'Hours' + str.slice(firstWords.length)
  let res = words[0].charAt(0).toUpperCase() + words[0].slice(1)
  if (words.length === 2 && words[1] !== 'id') {
    const second = words[1].charAt(0).toUpperCase() + words[1].slice(1)
    res += ' ' + second
  }
  return res + str.slice(firstWords.length)
}

</script>

<template>
  <div v-show="message">
    <p class="text-sm text-red-600">
      {{ convertFirstWordToTitleCase(message) }}
    </p>
  </div>
</template>
