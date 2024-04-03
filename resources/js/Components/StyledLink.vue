<script setup>
import {computed, h} from 'vue'
import {Link} from '@inertiajs/vue3'

const props = defineProps({
  href: {
    type: String,
    required: true,
  },
  active: {
    type: Boolean,
  },
  variant: {
    type: String,
    default: 'default'
  },
  icon: {
    type: [String]
  },
  title: {
    type: [String],
    default: 'Link'
  }
})

const classes = computed(() => {
  switch (props.variant) {
    case 'close':
      return 'bg-white overflow-hidden flex justify-end'

    case 'add':
      return 'bg-white overflow-hidden mb-6 flex justify-end'

    case 'nav':
      return props.active
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out'

    case 'responsive-nav':
      return props.active
        ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-left text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out'

    default:
      return 'bg-white overflow-hidden'
  }
})
</script>

<template>
  <Link :href="href" :class="classes">
    <font-awesome-icon v-if="icon" :icon="icon" size="xl" :title="title">
      <slot/>
    </font-awesome-icon>
    <span v-else>
      <slot/>
    </span>
  </Link>
</template>
