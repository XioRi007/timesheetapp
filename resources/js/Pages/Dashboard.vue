<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {Head} from '@inertiajs/vue3'
import MonthTable from "@/Components/MonthTable.vue"
import Title from "@/Components/Title.vue"
import Pagination from "@/Components/Pagination.vue"
</script>

<template>
  <Head title="Dashboard"/>

  <AuthenticatedLayout>
    <template #header>
      <Title text="Dashboard"/>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10">

          <h2 class="mb-10 text-xl font-semibold">Payment overview</h2>

          <div class="flex space-x-4 mb-10">
            <div
              class="basis-1/2 h-36 flex flex-col justify-center shadow-xl border border-blue-950 motion-safe:hover:scale-[1.01] cursor-pointer rounded-md">
              <h2 class="text-xl font-semibold">Total Paid</h2>
              <p class="text-3xl font-bold">{{ $page.props.payed }}</p>
            </div>
            <div
              class="basis-1/2 h-36 flex flex-col justify-center shadow-xl border border-blue-950 motion-safe:hover:scale-[1.01] cursor-pointer rounded-md">
              <h2 class="text-xl font-semibold">Total Unpaid</h2>
              <p class="text-3xl font-bold">{{ $page.props.unpayed }}</p>
            </div>
          </div>

          <h2 class="mt-20 mb-10 text-xl font-semibold">Timesheet</h2>
<!--          {{$page.props.table.data.data}}-->
          <MonthTable
            :table="$page.props.table.data.data"
            :month="$page.props.table.month"
            :year="$page.props.table.year"
            :daysInMonth="$page.props.table.daysInMonth"
            :currentMonth="$page.props.table.currentMonth"
            :prevMonth="$page.props.table.prevMonth"
            :nextMonth="$page.props.table.nextMonth"
          />
          <Pagination
            :links="$page.props.table.data.links"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
