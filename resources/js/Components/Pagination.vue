<script setup>
import {Link} from "@inertiajs/vue3"

defineProps({
  links: {
    type: Array,
    required: true
  }
})
</script>
<template>
  <div v-if="links.length > 3" dusk="pagination">
    <div class="flex flex-wrap -mb-1 justify-center mt-8">
      <template v-for="(link, p) in links" :key="p">
        <div v-if="link.url === null"
             class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded cursor-pointer"
             v-html="link.label"/>
        <Link v-else
              class="mr-1 mb-1 px-4 py-3 text-gray-200 text-sm bg-gray-700 leading-4 border rounded hover:bg-gray-600 focus:bg-gray-600 focus:bg-gray-600"
              :class="{ 'bg-gray-800 text-white': link.active }" :href="link.url" v-html="link.label"/>
      </template>
    </div>
  </div>
</template>