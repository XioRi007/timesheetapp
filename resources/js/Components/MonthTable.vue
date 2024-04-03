<script setup>
import {Link} from '@inertiajs/vue3'

defineProps({
  table: {
    type: Array,
  },
  month: {
    type: Number,
  },
  year: {
    type: Number,
  },
  daysInMonth: {
    type: Number,
  },
  currentMonth: {
    type: Object,
  },
  prevMonth: {
    type: Object,
  },
  nextMonth: {
    type: Object,
  },
})
</script>

<template>
  <div>
    <div class="flex justify-between mb-4">
      <Link :href="prevMonth.link" :only="['table']"
            class="bg-gray-800 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
        <font-awesome-icon class="mr-2" icon="fa-solid fa-backward"/>
        <span>{{ prevMonth.name }} {{ year }}</span>
      </Link>
      <Link :href="currentMonth.link" :only="['table']"
            class="bg-gray-800 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
        <span>{{ currentMonth.name }} {{ year }}</span>
      </Link>
      <Link :href="nextMonth.link" :only="['table']"
            class="bg-gray-800 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
        <span>{{ nextMonth.name }} {{ year }}</span>
        <font-awesome-icon class="ml-2" icon="fa-solid fa-forward"/>
      </Link>
    </div>
    <div
      v-if="table.length !== 0"
      class="relative overflow-x-auto shadow-md sm:rounded-lg">
      <table class="w-full text-sm text-left text-gray-300">
        <thead class="text-xs uppercase bg-gray-700 text-gray-300">
        <tr>
          <th class="px-2 py-2" scope="col">
            Developer
          </th>
          <th v-for="day in daysInMonth" :key="day" class="px-2 py-2" scope="col">{{ day }}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="developer in table" :key="developer.id"
            class="border-b bg-gray-800 border-gray-700 hover:bg-gray-600">
          <th class="px-2 py-1 font-medium whitespace-nowrap text-white" scope="row">
            {{ developer.name }}
          </th>
          <td v-for="day in daysInMonth" :key="day" class="px-2 py-1">{{ developer.hours[day - 1] }}</td>
        </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="my-10">
      No work logs was reported for this month
    </p>

  </div>
</template>
